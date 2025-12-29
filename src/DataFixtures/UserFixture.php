<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\DataFixtures\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends BaseFixture
{
    public const GESTIONNAIRE_REFERENCE = 'user-gestionnaire';
    public const CLIENT_REFERENCE = 'user-client';

    public function load(ObjectManager $manager): void
    {
        // Gestionnaire
        $gestionnaire = new Users();
        $gestionnaire->setId(1);
        $gestionnaire->setLogin('admin@brasilburger.sn');
        $gestionnaire->setPassword_hash(password_hash('admin123', PASSWORD_BCRYPT));
        $gestionnaire->setRole_users('GESTIONNAIRE');
        
        $manager->persist($gestionnaire);
        $this->addReference(self::GESTIONNAIRE_REFERENCE, $gestionnaire);

        // Client de test
        $client = new Users();
        $client->setId(2);
        $client->setLogin('client@test.com');
        $client->setPassword_hash(password_hash('client123', PASSWORD_BCRYPT));
        $client->setRole_users('CLIENT');
        
        $manager->persist($client);
        $this->addReference(self::CLIENT_REFERENCE, $client);

        $manager->flush();
    }
}