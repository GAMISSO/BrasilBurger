<?php
// src/Service/LivraisonService.php
namespace App\Service\Impl;

use App\Entity\OrderTable;
use App\Entity\DeliveryAssignment;
use App\Service\LivraisonService;
use App\Entity\Livreur;
use Doctrine\ORM\EntityManagerInterface;

class LivraisonServiceImpl implements LivraisonService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Obtenir les commandes à livrer groupées par zone
     */
    public function getCommandesParZone(): array
    {
        try {
            // Récupérer toutes les commandes à livrer (peu importe l'état) pour les afficher
            $dql = "SELECT o FROM App\\Entity\\OrderTable o 
                WHERE o.type_livraison = :type
                ORDER BY o.zone_id ASC, o.created_at ASC";
            
            $commandes = $this->entityManager->createQuery($dql)
            ->setParameter('type', 'A_livrer')
            ->getResult();

            // Grouper par zone
            $commandesParZone = [];
            foreach ($commandes as $commande) {
                $zoneId = $commande->getZone_id();
                if ($zoneId) {
                    $zone = $this->entityManager->getRepository(\App\Entity\Zones::class)->find($zoneId);
                    if ($zone) {
                        $zoneKey = $zone->getNom();
                        if (!isset($commandesParZone[$zoneKey])) {
                            $commandesParZone[$zoneKey] = [
                                'zone' => $zone,
                                'commandes' => []
                            ];
                        }
                        $commandesParZone[$zoneKey]['commandes'][] = $commande;
                    }
                }
            }

            return $commandesParZone;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération des commandes: ' . $e->getMessage());
        }
    }

    /**
     * Affecter un livreur à des commandes
     */
    public function affecterLivreur(array $commandeIds, int $livreurId): void
    {
        $livreur = $this->entityManager->getRepository(Livreur::class)->find($livreurId);
        
        if (!$livreur) {
            throw new \InvalidArgumentException("Livreur non trouvé");
        }

        foreach ($commandeIds as $commandeId) {
            $commande = $this->entityManager->getRepository(OrderTable::class)->find($commandeId);
            
            if ($commande && $commande->getType_livraison() === 'A_livrer') {
                // Créer l'affectation
                $assignment = new DeliveryAssignment();
                $assignment->setLivreur($livreur);
                $assignment->setAssigned_at(new \DateTime());
                $assignment->setStatus('AFFECTE');
                
                $this->entityManager->persist($assignment);
                
                // Changer l'état de la commande à En_cours
                $commande->setState_order('En_cours');
                $commande->setUpdated_at(new \DateTime());
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Obtenir les livreurs disponibles
     */
    public function getLivreursDisponibles(): array
    {
        return $this->entityManager->getRepository(Livreur::class)
            ->findBy(['disponibilite' => 'Disponible']);
    }

    /**
     * Obtenir les livraisons d'un livreur
     */
    public function getLivraisonsLivreur(int $livreurId): array
    {
        $qb = $this->entityManager->getRepository(DeliveryAssignment::class)->createQueryBuilder('da');
        
        return $qb->where('da.livreur = :livreur')
            ->andWhere('da.statut != :termine')
            ->setParameter('livreur', $livreurId)
            ->setParameter('termine', 'TERMINE')
            ->orderBy('da.assigned_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}