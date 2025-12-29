<?php

namespace App\DataFixtures;

use App\Entity\Burger;
use App\DataFixtures\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class BurgerFixture extends BaseFixture
{
    public const BURGER_CLASSIC = 'burger-classic';
    public const BURGER_SALAD = 'burger-salad';

    public function load(ObjectManager $manager): void
    {
        $burgers = [
            [
                'id' => 1,
                'nom' => 'Brasil Burger Classic',
                'prix' => 5500,
                'image_url' => 'burger-classic.jpg',
                'reference' => self::BURGER_CLASSIC
            ],
            [
                'id' => 2,
                'nom' => 'Brasil Burger Salad',
                'prix' => 6000,
                'image_url' => 'burger-salad.jpg',
                'reference' => self::BURGER_SALAD
            ],
            [
                'id' => 3,
                'nom' => 'Brasil Burger Cheese',
                'prix' => 7000,
                'image_url' => 'burger-cheese.jpg',
                'reference' => 'burger-cheese'
            ],
            [
                'id' => 4,
                'nom' => 'Brasil Burger Bacon',
                'prix' => 7500,
                'image_url' => 'burger-bacon.jpg',
                'reference' => 'burger-bacon'
            ],
            [
                'id' => 5,
                'nom' => 'Brasil Burger Double',
                'prix' => 8500,
                'image_url' => 'burger-double.jpg',
                'reference' => 'burger-double'
            ],
        ];

        foreach ($burgers as $data) {
            $burger = new Burger();
            $burger->setId($data['id']);
            $burger->setNom($data['nom']);
            $burger->setPrix($data['prix']);
            $burger->setImage_url($data['image_url']);
            $burger->setCreated_at(new \DateTime());

            $manager->persist($burger);
            $this->addReference($data['reference'], $burger);
        }

        $manager->flush();
    }
}