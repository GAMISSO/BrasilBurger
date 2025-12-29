<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class AppFixture extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        // Cette classe principale peut orchestrer le chargement
        // Les fixtures spécifiques sont dans leurs propres classes
        $manager->flush();
    }
}