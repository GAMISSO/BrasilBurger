<?php

namespace App\Command;

use App\DataFixtures\AppFixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fixtures:load', description: 'Load application fixtures (AppFixture)')]
class LoadAppFixturesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Loading fixtures (custom loader)...</info>');

        try {
            // Order matters: create and run each fixture manually
            $fixtures = [
                new \App\DataFixtures\BurgerFixture(),
                new \App\DataFixtures\ComplementFixture(),
                new \App\DataFixtures\MenuFixture(),
                new \App\DataFixtures\UserFixture(),
                new \App\DataFixtures\ZoneFixture(),
                new \App\DataFixtures\LivreurFixture(),
            ];

            foreach ($fixtures as $f) {
                if (method_exists($f, 'load')) {
                    $f->load($this->em);
                }
            }

            $count = $this->em->getRepository(\App\Entity\Burger::class)->count([]);
            $output->writeln('<info>Fixtures loaded successfully. Burger count: '.$count.'</info>');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('<error>Fixture load failed: '.$e->getMessage().'</error>');
            $output->writeln('<error>'.$e->getTraceAsString().'</error>');
            return Command::FAILURE;
        }
    }
}
