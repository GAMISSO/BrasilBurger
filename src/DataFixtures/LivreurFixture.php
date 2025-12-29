<?php

namespace App\DataFixtures;

use App\Entity\Livreur;
use App\DataFixtures\BaseFixture;
use Doctrine\Persistence\ObjectManager;

class LivreurFixture extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        $livreurs = [
            [
                'id' => 1,
                'nom' => 'Moussa Diop',
                'disponibilite' => 'Disponible'
            ],
            [
                'id' => 2,
                'nom' => 'Fatou Fall',
                'disponibilite' => 'Disponible'
            ],
            [
                'id' => 3,
                'nom' => 'Ibrahima Ndiaye',
                'disponibilite' => 'Disponible'
            ],
            [
                'id' => 4,
                'nom' => 'Aissatou Sow',
                'disponibilite' => 'OCCUPE'
            ],
        ];

        foreach ($livreurs as $data) {
            $livreur = new Livreur();
            $livreur->setId($data['id']);
            $livreur->setNom($data['nom']);
            $livreur->setDisponibilite($data['disponibilite']);

            $manager->persist($livreur);
        }

        $manager->flush();
    }
}