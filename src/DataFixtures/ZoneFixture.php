<?php

namespace App\DataFixtures;

use App\Entity\Zones;
use App\DataFixtures\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class ZoneFixture extends BaseFixture
{
    public const ZONE_PLATEAU = 'zone-plateau';
    public const ZONE_ALMADIES = 'zone-almadies';

    public function load(ObjectManager $manager): void
    {
        $zones = [
            [
                'id' => 1,
                'nom' => 'Plateau',
                'prix_zone' => 1500,
                'reference' => self::ZONE_PLATEAU
            ],
            [
                'id' => 2,
                'nom' => 'Almadies',
                'prix_zone' => 2500,
                'reference' => self::ZONE_ALMADIES
            ],
            [
                'id' => 3,
                'nom' => 'Sacré-Coeur',
                'prix_zone' => 2000,
                'reference' => 'zone-sacrecoeur'
            ],
            [
                'id' => 4,
                'nom' => 'Pikine',
                'prix_zone' => 3000,
                'reference' => 'zone-pikine'
            ],
        ];

        foreach ($zones as $data) {
            $zone = new Zones();
            $zone->setId($data['id']);
            $zone->setNom($data['nom']);
            $zone->setPrix_zone($data['prix_zone']);
            $zone->setCreated_at(new \DateTime());

            $manager->persist($zone);
            $this->addReference($data['reference'], $zone);
        }

        $manager->flush();
    }
}