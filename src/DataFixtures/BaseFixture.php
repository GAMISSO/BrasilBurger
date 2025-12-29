<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;

/**
 * Simple local replacement for Doctrine's Fixture base when the bundle
 * is not installed. Provides add/get reference functionality shared
 * between fixture instances via a static repository.
 */
class BaseFixture
{
    protected static array $references = [];

    protected function addReference(string $name, $object): void
    {
        self::$references[$name] = $object;
    }

    protected function getReference(string $name)
    {
        return self::$references[$name] ?? null;
    }

    /**
     * Optional hook for fixtures that expect a load(ObjectManager) method
     */
    public function load(ObjectManager $manager): void
    {
        // noop by default
    }
}
