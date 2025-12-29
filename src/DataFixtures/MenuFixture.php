<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use App\DataFixtures\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class MenuFixture extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        $menus = [
            [
                'id' => 1,
                'nom' => 'Menu Enfant',
                'burger_id' => 1, // Reference to burger
                'prix_total' => 7000,
                'image' => 'menu-enfant.jpg'
            ],
            [
                'id' => 2,
                'nom' => 'Menu Classic',
                'burger_id' => 1,
                'prix_total' => 8000,
                'image' => 'menu-classic.jpg'
            ],
            [
                'id' => 3,
                'nom' => 'Menu Complet',
                'burger_id' => 3,
                'prix_total' => 10500,
                'image' => 'menu-complet.jpg'
            ],
        ];

        foreach ($menus as $data) {
            $menu = new Menu();
            $menu->setId($data['id']);
            $menu->setNom($data['nom']);
            $menu->setBurger_id($data['burger_id']);
            $menu->setPrix_total($data['prix_total']);
            $menu->setImage($data['image']);
            $menu->setCreated_at(new \DateTime());

            $manager->persist($menu);
        }

        $manager->flush();
    }


}