<?php
require __DIR__ . '/../vendor/autoload.php';

$classes = [
    'App\\Controller\\Admin\\BurgerController',
    'App\\Controller\\Admin\\ComplementController',
];

foreach ($classes as $class) {
    echo "Class: $class\n";
    if (!class_exists($class)) {
        echo "  class not found\n";
        continue;
    }
    $r = new ReflectionClass($class);
    foreach ($r->getAttributes() as $a) {
        echo '  attribute: ' . $a->getName() . PHP_EOL;
    }
    foreach ($r->getMethods() as $m) {
        foreach ($m->getAttributes() as $a) {
            echo '  method ' . $m->getName() . ' attribute: ' . $a->getName() . PHP_EOL;
        }
    }
}
