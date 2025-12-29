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
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        $commandes = $qb->where('o.type_livraison = :type')
            ->andWhere('o.state_order IN (:states)')
            ->setParameter('type', 'A_livrer')
            ->setParameter('states', ['En_cours'])
            ->orderBy('o.zone_id', 'ASC')
            ->addOrderBy('o.created_at', 'ASC')
            ->getQuery()
            ->getResult();

        // Grouper par zone avec les objets complets
        $commandesParZone = [];
        foreach ($commandes as $commande) {
            $zoneId = $commande->getZone_id();
            if ($zoneId) {
                // Récupérer l'objet zone complet
                $zone = $this->entityManager->getRepository(\App\Entity\Zones::class)->find($zoneId);
                if ($zone) {
                    $zoneKey = $zone->getNom();
                    if (!isset($commandesParZone[$zoneKey])) {
                        $commandesParZone[$zoneKey] = [
                            'zone' => $zone,
                            'commandes' => []
                        ];
                    }
                    
                    // Enrichir la commande avec les données du client et livreur
                    $clientProfilId = $commande->getClient_profil_id();
                    if ($clientProfilId) {
                        $clientProfil = $this->entityManager->getRepository(\App\Entity\ClientProfil::class)->find($clientProfilId);
                        if ($clientProfil) {
                            $commande->clientProfil = $clientProfil;
                        }
                    }
                    
                    // Chercher l'affectation de livreur
                    $assignment = $this->entityManager->getRepository(DeliveryAssignment::class)
                        ->findOneBy(['id' => $commande->getId()]);
                    if ($assignment && $assignment->getLivreur_id()) {
                        $livreur = $this->entityManager->getRepository(Livreur::class)->find($assignment->getLivreur_id());
                        if ($livreur) {
                            $commande->livreur = $livreur;
                        }
                    }
                    
                    $commandesParZone[$zoneKey]['commandes'][] = $commande;
                }
            }
        }

        return $commandesParZone;
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
                $assignment->setLivreur_id($livreurId);
                $assignment->setAssigned_at(new \DateTime());
                $assignment->setStatut('AFFECTE');
                
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
        
        return $qb->where('da.livreur_id = :livreur')
            ->andWhere('da.statut != :termine')
            ->setParameter('livreur', $livreurId)
            ->setParameter('termine', 'TERMINE')
            ->orderBy('da.assigned_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}