<?php

namespace App\DataFixtures;

use App\Entity\Complement;
use App\DataFixtures\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class ComplementFixture extends BaseFixture
{
    public const COMPLEMENT_COCA = 'complement-coca';
    public const COMPLEMENT_FRITE = 'complement-frite';

    public function load(ObjectManager $manager): void
    {
        $complements = [
            [
                'id' => 1,
                'nom' => 'Coca Cola',
                'type' => 'BOISSON',
                'prix' => 1000,
                'image' => 'coca.jpg',
                'reference' => self::COMPLEMENT_COCA
            ],
            [
                'id' => 2,
                'nom' => 'Fanta Orange',
                'type' => 'BOISSON',
                'prix' => 1000,
                'image' => 'fanta.jpg',
                'reference' => 'complement-fanta'
            ],
            [
                'id' => 3,
                'nom' => 'Sprite',
                'type' => 'BOISSON',
                'prix' => 1000,
                'image' => 'sprite.jpg',
                'reference' => 'complement-sprite'
            ],
            [
                'id' => 4,
                'nom' => 'Eau Minérale',
                'type' => 'BOISSON',
                'prix' => 500,
                'image' => 'eau.jpg',
                'reference' => 'complement-eau'
            ],
            [
                'id' => 5,
                'nom' => 'Frites Classiques',
                'type' => 'FRITE',
                'prix' => 1500,
                'image' => 'frites.jpg',
                'reference' => self::COMPLEMENT_FRITE
            ],
            [
                'id' => 6,
                'nom' => 'Frites Cheddar',
                'type' => 'FRITE',
                'prix' => 2000,
                'image' => 'frites-cheddar.jpg',
                'reference' => 'complement-frite-cheddar'
            ],
        ];

        foreach ($complements as $data) {
            $complement = new Complement();
            $complement->setId($data['id']);
            $complement->setNom($data['nom']);
            $complement->setType_complement($data['type']);
            $complement->setPrix($data['prix']);
            $complement->setImage($data['image']);
            $complement->setCreated_at(new \DateTime());

            $manager->persist($complement);
            $this->addReference($data['reference'], $complement);
        }

        $manager->flush();
    }
}