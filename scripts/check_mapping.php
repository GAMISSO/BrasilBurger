<?php
require __DIR__ . '/../vendor/autoload.php';
$kernel = new App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();
$doctrine = $container->get('doctrine');
$em = $doctrine->getManager();
$meta = $em->getMetadataFactory()->getAllMetadata();
foreach ($meta as $m) {
    echo $m->getName() . PHP_EOL;
    $ids = $m->getIdentifier();
    if (empty($ids)) {
        echo "  -> NO IDENTIFIER\n";
    } else {
        echo "  -> identifier: " . implode(',', $ids) . "\n";
    }
}
