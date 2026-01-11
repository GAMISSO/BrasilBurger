<?php
// src/Service/StatistiqueService.php
namespace App\Service\Impl;

use App\Entity\OrderTable;
use App\Entity\OrderLine;
use App\Service\StatistiqueService;
use App\Entity\Payement;
use Doctrine\ORM\EntityManagerInterface;

class StatistiqueServiceImpl implements StatistiqueService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Statistiques du jour
     */
    public function getStatistiquesJour(\DateTime $date): array
    {
        $dateStr = $date->format('Y-m-d');

        return [
            'commandes_en_cours' => $this->getCommandesEnCours($dateStr),
            'commandes_validees' => $this->getCommandesValidees($dateStr),
            'recettes_journalieres' => $this->getRecettesJournalieres($dateStr),
            'produits_populaires' => $this->getProduitsPopulaires($dateStr),
            'commandes_annulees' => $this->getCommandesAnnulees($dateStr),
        ];
    }

    /**
     * Nombre de commandes en cours
     */
    private function getCommandesEnCours(string $date): int
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        return $qb->select('COUNT(o.id)')
            ->where('o.created_at = :date')
            ->andWhere('o.state_order IN (:states)')
            ->setParameter('date', new \DateTimeImmutable($date))
            ->setParameter('states', ['En_cours'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Nombre de commandes validées
     */
    private function getCommandesValidees(string $date): int
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        return $qb->select('COUNT(o.id)')
            ->where('o.created_at = :date')
            ->andWhere('o.state_order = :state')
            ->setParameter('date', new \DateTimeImmutable($date))
            ->setParameter('state', 'En_cours')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recettes journalières
     */
    private function getRecettesJournalieres(string $date): float
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        // Additionne tous les montants du jour (même si le paiement est en attente)
        $result = $qb->select('COALESCE(SUM(o.total_prix), 0)')
            ->where('o.created_at = :date')
            ->setParameter('date', new \DateTimeImmutable($date))
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }

    /**
     * Produits les plus vendus
     */
    private function getProduitsPopulaires(string $date, int $limit = 10): array
    {
        $qb = $this->entityManager->getRepository(OrderLine::class)->createQueryBuilder('ol');
        
        return $qb->select('ol.item_type, ol.burger_id, ol.menu_id, SUM(ol.quantity) as total_ventes')
            ->leftJoin(OrderTable::class, 'o', 'WITH', 'ol.order_id = o.id')
            ->where('o.created_at = :date')
            ->andWhere('o.state_order != :cancelled')
            ->setParameter('date', new \DateTimeImmutable($date))
            ->setParameter('cancelled', 'Terminee')
            ->groupBy('ol.item_type', 'ol.burger_id', 'ol.menu_id')
            ->orderBy('total_ventes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Commandes annulées
     */
    private function getCommandesAnnulees(string $date): int
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        return $qb->select('COUNT(o.id)')
            ->where('o.created_at = :date')
            ->andWhere('o.state_order = :state')
            ->setParameter('date', new \DateTimeImmutable($date))
            ->setParameter('state', 'Terminee')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Statistiques par période
     */
    public function getStatistiquesPeriode(\DateTime $debut, \DateTime $fin): array
    {
        return [
            'total_commandes' => $this->getTotalCommandesPeriode($debut, $fin),
            'total_recettes' => $this->getTotalRecettesPeriode($debut, $fin),
            'commandes_par_jour' => $this->getCommandesParJour($debut, $fin),
            'produits_populaires' => $this->getProduitsPopulairesPeriode($debut, $fin),
        ];
    }

    private function getTotalCommandesPeriode(\DateTime $debut, \DateTime $fin): int
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        return $qb->select('COUNT(o.id)')
            ->where('o.created_at BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getTotalRecettesPeriode(\DateTime $debut, \DateTime $fin): float
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        // Additionne tous les montants sur la période
        $result = $qb->select('COALESCE(SUM(o.total_prix), 0)')
            ->where('o.created_at BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }

    private function getCommandesParJour(\DateTime $debut, \DateTime $fin): array
    {
        $qb = $this->entityManager->getRepository(OrderTable::class)->createQueryBuilder('o');
        
        return $qb->select('o.created_at as date, COUNT(o.id) as total')
            ->where('o.created_at BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    private function getProduitsPopulairesPeriode(\DateTime $debut, \DateTime $fin, int $limit = 20): array
    {
        $qb = $this->entityManager->getRepository(OrderLine::class)->createQueryBuilder('ol');
        
        return $qb->select('ol.item_type, ol.burger_id, ol.menu_id, SUM(ol.quantity) as total_ventes')
            ->leftJoin(OrderTable::class, 'o', 'WITH', 'ol.order_id = o.id')
            ->where('o.created_at BETWEEN :debut AND :fin')
            ->andWhere('o.state_order != :cancelled')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->setParameter('cancelled', 'Terminee')
            ->groupBy('ol.item_type', 'ol.burger_id', 'ol.menu_id')
            ->orderBy('total_ventes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}