<?php
namespace App\Service;

use App\Entity\OrderTable;
use App\Entity\Zones;
use App\Entity\Livreur;
use App\Entity\DeliveryAssignment;
use Doctrine\ORM\EntityManagerInterface;

class LivraisonServiceImpl implements LivraisonService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function getCommandesParZone(): array
    {
        $zonesRepo = $this->entityManager->getRepository(Zones::class);
        $commandesRepo = $this->entityManager->getRepository(OrderTable::class);
        
        $zones = $zonesRepo->findAll();
        $result = [];

        foreach ($zones as $zone) {
            // Récupérer les commandes pour cette zone
            $commandes = $commandesRepo->findBy(
                ['zone_id' => $zone->getId()],
                ['created_at' => 'DESC']
            );

            $result[$zone->getNom()] = [
                'zone' => $zone,
                'commandes' => $commandes,
            ];
        }

        return $result;
    }

    public function affecterLivreur(array $commandeIds, int $livreurId): void
    {
        $commandesRepo = $this->entityManager->getRepository(OrderTable::class);
        $livreurRepo = $this->entityManager->getRepository(Livreur::class);
        
        $livreur = $livreurRepo->find($livreurId);
        if (!$livreur) {
            throw new \Exception('Livreur non trouvé');
        }

        foreach ($commandeIds as $commandeId) {
            $commande = $commandesRepo->find($commandeId);
            if ($commande) {
                // Créer l'affectation
                $assignment = new DeliveryAssignment();
                $assignment->setOrder($commande);
                $assignment->setLivreur($livreur);
                $assignment->setAssigned_at(new \DateTime());
                $assignment->setStatus('assigned');

                $this->entityManager->persist($assignment);
            }
        }

        $this->entityManager->flush();
    }

    public function getLivreursDisponibles(): array
    {
        $livreurRepo = $this->entityManager->getRepository(Livreur::class);
        return $livreurRepo->findBy(['disponibilite' => 'disponible']);
    }

    public function getLivraisonsLivreur(int $livreurId): array
    {
        $assignmentRepo = $this->entityManager->getRepository(DeliveryAssignment::class);
        return $assignmentRepo->findBy(['livreur' => $livreurId]);
    }
}
