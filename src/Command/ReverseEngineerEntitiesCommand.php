<?php

namespace App\Command;

use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:doctrine:reverse-engineer',
    description: 'Reverse-engineer database tables into basic Doctrine entity classes (attributes).'
)]
class ReverseEngineerEntitiesCommand extends Command
{
    private ManagerRegistry $doctrine;
    private string $projectDir;

    public function __construct(ManagerRegistry $doctrine, KernelInterface $kernel)
    {
        $this->doctrine = $doctrine;
        $this->projectDir = $kernel->getProjectDir();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite existing entity files')
            ->setHelp(<<<'HELP'
This command connects to the configured database, inspects tables and generates plain Doctrine entities
(with attributes) in src/Entity. Only basic column mappings are generated; relations (foreign keys)
are left for manual implementation.

Usage: php bin/console app:doctrine:reverse-engineer [--force]
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conn = $this->doctrine->getConnection();

        $output->writeln('Connecting to database...');

        try {
            $schemaManager = $conn->createSchemaManager();
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed to get schema manager: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Attempt listing tables; if the DB has custom enum types unknown to DBAL, register them as string and retry
        $attempts = 0;
        while (true) {
            try {
                $tables = $schemaManager->listTables();
                break;
            } catch (\Doctrine\DBAL\Exception\InvalidArgumentException $e) {
                if (preg_match('/Unknown database type "(?P<type>[^\"]+)" requested/', $e->getMessage(), $m)) {
                    $type = $m['type'];
                    $output->writeln("<comment>Registering unknown database type '$type' as string and retrying...</comment>");
                    $conn->getDatabasePlatform()->registerDoctrineTypeMapping($type, 'string');
                    $attempts++;
                    if ($attempts > 10) {
                        $output->writeln('<error>Too many unknown types encountered, aborting.</error>');
                        return Command::FAILURE;
                    }
                    continue;
                }

                $output->writeln('<error>Failed to list tables: ' . $e->getMessage() . '</error>');
                return Command::FAILURE;
            }
        }

        if (empty($tables)) {
            $output->writeln('<comment>No tables found in the database.</comment>');
            return Command::SUCCESS;
        }

        $fs = new Filesystem();
        $dir = $this->projectDir . '/src/Entity';
        if (!is_dir($dir)) {
            $fs->mkdir($dir);
        }

        foreach ($tables as $table) {
            $tableName = $table->getName();
            $className = $this->tableNameToClassName($tableName);
            $file = $dir . '/' . $className . '.php';

            if (file_exists($file) && !$input->getOption('force')) {
                $output->writeln("<comment>Skip: $className already exists (use --force to overwrite)</comment>");
                continue;
            }

            $content = $this->generateEntityContent($className, $table);

            $fs->dumpFile($file, $content);
            $output->writeln("<info>Generated:</info> src/Entity/$className.php");
        }

        $output->writeln('<info>Reverse engineering complete.</info>');
        return Command::SUCCESS;
    }

    private function tableNameToClassName(string $tableName): string
    {
        // remove prefixes if any and convert snake_case to PascalCase
        $parts = preg_split('/[_\-]/', $tableName);
        $parts = array_map('ucfirst', $parts);
        return implode('', $parts);
    }

    private function generateEntityContent(string $className, \Doctrine\DBAL\Schema\Table $table): string
    {
        $lines = [];
        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = 'namespace App\\Entity;';
        $lines[] = '';
        $lines[] = 'use Doctrine\\ORM\\Mapping as ORM;';
        $lines[] = '';
        $lines[] = "#[ORM\\Entity]";
        $lines[] = "#[ORM\\Table(name: '" . $table->getName() . "')]";
        $lines[] = "class $className";
        $lines[] = '{';

        foreach ($table->getColumns() as $column) {
            $colName = $column->getName();
            $isAutoincrement = $column->getAutoincrement();
            $typeObj = $column->getType();
            
            // Derive type name from the class name, e.g. IntegerType => integer
            $class = get_class($typeObj);
            $short = substr($class, strrpos($class, '\\') + 1);
            $type = strtolower(preg_replace('/Type$/', '', $short));
            
            $nullable = !$column->getNotnull();
            $length = $column->getLength();

            $doctrineType = $this->mapColumnTypeToDoctrineType($type);
            $phpType = $this->mapDoctrineTypeToPhpType($doctrineType);

            // Attributes
            if ($isAutoincrement) {
                $lines[] = "    #[ORM\\Id]";
                $lines[] = "    #[ORM\\GeneratedValue]";
                $lines[] = "    #[ORM\\Column(type: '$doctrineType')]";
                $lines[] = '    private ?' . $phpType . ' $' . $colName . ' = null;';
            } else {
                $colOptions = [];
                $colOptions[] = "type: '$doctrineType'";
                if ($length && in_array($doctrineType, ['string'])) {
                    $colOptions[] = "length: $length";
                }
                if ($nullable) {
                    $colOptions[] = 'nullable: true';
                }
                $lines[] = '    #[ORM\\Column(' . implode(', ', $colOptions) . ')]';
                $lines[] = '    private ' . ($nullable ? '?' : '') . $phpType . ' $' . $colName . ($nullable ? ' = null;' : ';');
            }
            $lines[] = '';

            // Getter
            $methodName = 'get' . ucfirst($colName);
            $lines[] = '    public function ' . $methodName . '(): ' . ($nullable ? '?' : '') . $phpType . ' {';
            $lines[] = '        return $this->' . $colName . ';';
            $lines[] = '    }';
            $lines[] = '';

            // Setter (skip for id/autoincrement)
            if (!$isAutoincrement) {
                $methodName = 'set' . ucfirst($colName);
                $lines[] = '    public function ' . $methodName . '(' . ($nullable ? '?' : '') . $phpType . ' $' . $colName . '): self {';
                $lines[] = '        $this->' . $colName . ' = $' . $colName . ';';
                $lines[] = '        return $this;';
                $lines[] = '    }';
                $lines[] = '';
            }
        }

        $lines[] = '}';
        $lines[] = '';

        return implode("\n", $lines);
    }

    private function mapColumnTypeToDoctrineType(string $dbalType): string
    {
        // Map DBAL types to Doctrine column types used in attributes
        return match ($dbalType) {
            Types::BIGINT => 'bigint',
            Types::SMALLINT => 'smallint',
            Types::INTEGER => 'integer',
            Types::FLOAT => 'float',
            Types::DECIMAL => 'decimal',
            Types::TEXT => 'text',
            Types::STRING => 'string',
            Types::BOOLEAN => 'boolean',
            Types::DATE_IMMUTABLE, Types::DATE_MUTABLE => 'date',
            Types::DATETIME_IMMUTABLE, Types::DATETIME_MUTABLE => 'datetime',
            Types::DATETIMETZ_IMMUTABLE, Types::DATETIMETZ_MUTABLE => 'datetimetz',
            default => 'string',
        };
    }

    private function mapDoctrineTypeToPhpType(string $doctrineType): string
    {
        return match ($doctrineType) {
            'bigint', 'smallint', 'integer' => 'int',
            'float', 'decimal' => 'float',
            'boolean' => 'bool',
            'datetime', 'datetimetz', 'date' => '\\DateTimeInterface',
            default => 'string',
        };
    }
}
