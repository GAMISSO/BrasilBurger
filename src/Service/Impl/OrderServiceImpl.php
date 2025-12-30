<?php
// src/Service/Impl/OrderServiceImpl.php
namespace App\Service\Impl;

use App\Entity\OrderTable;
use App\Entity\OrderLine;
use App\Service\OrderService;
use App\Entity\Payement;
use App\Repository\OrderTableRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderServiceImpl implements OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Mapper un état Symfony à un état PostgreSQL valide
     */
    private function mapStateToDatabase(string $state): string
    {
        // Mapper les états Symfony aux 2 états disponibles en PostgreSQL
        $stateMap = [
            'EN_ATTENTE' => 'En_cours',
            'EN_COURS_VALIDATION' => 'En_cours',
            'VALIDEE' => 'En_cours',
            'EN_PREPARATION' => 'En_cours',
            'PRETE' => 'En_cours',
            'EN_LIVRAISON' => 'En_cours',
            'TERMINEE' => 'Terminee',
            'ANNULEE' => 'Terminee',
            'En_cours' => 'En_cours',
            'Terminee' => 'Terminee',
        ];
        
        return $stateMap[$state] ?? 'En_cours';
    }

    /**
     * Créer une nouvelle commande
     */
    public function creerCommande(array $data): OrderTable
    {
        $commande = new OrderTable();
        $state = $this->mapStateToDatabase($data['state_order'] ?? 'EN_ATTENTE');
        $commande->setState_order($state);
        $commande->setType_livraison($data['type_livraison']);
        $commande->setAdresse_livraison($data['adresse_livraison'] ?? null);
        $commande->setTotal_prix($data['total_prix']);
        $commande->setCreated_at(new \DateTime());
        $commande->setClient_profil_id($data['client_profil_id'] ?? null);
        $commande->setZone($data['zone'] ?? null);

        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        return $commande;
    }

    /**
     * Ajouter une ligne de commande
     */
    public function ajouterLigneCommande(OrderTable $commande, array $itemData): OrderLine
    {
        $ligne = new OrderLine();
        $ligne->setOrder_id($commande->getId());
        $ligne->setItem_type($itemData['item_type']);
        $ligne->setBurger_id($itemData['burger_id'] ?? null);
        $ligne->setMenu_id($itemData['menu_id'] ?? null);
        $ligne->setQuantity($itemData['quantity']);
        $ligne->setPrix($itemData['prix']);
        $ligne->setCreated_at(new \DateTime());

        $this->entityManager->persist($ligne);
        $this->entityManager->flush();

        return $ligne;
    }

    /**
     * Changer l'état d'une commande
     */
    public function changerEtat(OrderTable $commande, string $nouvelEtat): void
    {
        $etatsValides = ['EN_ATTENTE', 'EN_COURS_VALIDATION', 'VALIDEE', 'EN_PREPARATION', 'PRETE', 'EN_LIVRAISON', 'TERMINEE', 'ANNULEE'];
        
        if (!in_array($nouvelEtat, $etatsValides)) {
            throw new \InvalidArgumentException("État invalide: $nouvelEtat");
        }

        $state = $this->mapStateToDatabase($nouvelEtat);
        $commande->setState_order($state);
        $commande->setUpdated_at(new \DateTime());

        $this->entityManager->flush();
    }

    /**
     * Annuler une commande
     */
    public function annulerCommande(OrderTable $commande): void
    {
        if (!$this->peutEtreAnnulee($commande)) {
            throw new \LogicException("Cette commande ne peut pas être annulée");
        }

        $this->changerEtat($commande, 'Terminee');
    }

    /**
     * Marquer une commande comme terminée
     */
    public function terminerCommande(OrderTable $commande): void
    {
        $this->changerEtat($commande, 'TERMINEE');
    }

    /**
     * Vérifier si une commande peut être annulée
     */
    public function peutEtreAnnulee(OrderTable $commande): bool
    {
        $etatsNonAnnulables = ['Terminee', 'En_cours'];
        return !in_array($commande->getState_order(), $etatsNonAnnulables);
    }

    /**
     * Calculer le montant total d'une commande
     */
    public function calculerMontantTotal(OrderTable $commande): int
    {
        $repository = $this->entityManager->getRepository(OrderLine::class);
        $lignes = $repository->findBy(['order_id' => $commande->getId()]);

        $total = 0;
        foreach ($lignes as $ligne) {
            $total += $ligne->getPrix() * $ligne->getQuantity();
        }

        // Ajouter frais de livraison si nécessaire
        if ($commande->getType_livraison() === 'A_livrer' && $commande->getZone()->getId()) {
            $zone = $this->entityManager->getRepository('App\Entity\Zones')->find($commande->getZone()->getId());
            if ($zone) {
                $total += $zone->getPrix_zone();
            }
        }

        return $total;
    }

    /**
     * Obtenir les commandes filtrées
     */
    public function getCommandesFilters(array $filters = []): array
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');

        if (!empty($filters['state_order'])) {
            $mappedState = $this->mapStateToDatabase($filters['state_order']);
            $qb->andWhere('o.state_order = :state')
               ->setParameter('state', $mappedState);
        }

        if (!empty($filters['date'])) {
            try {
                $date = is_string($filters['date']) 
                    ? new \DateTime($filters['date']) 
                    : $filters['date'];
                // Comparer la date en utilisant CAST pour PostgreSQL
                $qb->andWhere('CAST(o.created_at AS DATE) = :date')
                   ->setParameter('date', $date->format('Y-m-d'));
            } catch (\Exception $e) {
                // Ignorer le filtre de date si invalide
            }
        }

        if (!empty($filters['client_id'])) {
            $qb->andWhere('o.client_profil_id = :client')
               ->setParameter('client', (int)$filters['client_id']);
        }

        $qb->orderBy('o.created_at', 'DESC');

        return $qb->getQuery()->getResult();
    }
}