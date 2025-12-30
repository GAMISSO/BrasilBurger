<?php
// src/Service/PaiementService.php
namespace App\Service\Impl;

use App\Entity\Payement;
use App\Entity\OrderTable;
use App\Service\PaiementService;
use Doctrine\ORM\EntityManagerInterface;

class PaiementServiceImpl implements PaiementService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Enregistrer un paiement
     */
    public function enregistrerPaiement(OrderTable $commande, array $data): Payement
    {
        $paiement = new Payement();
        $paiement->setMethode_payement($data['methode_payement']);
        $paiement->setMontant($commande->getTotal_prix());
        $paiement->setTransaction_ref($data['transaction_ref'] ?? null);
        $paiement->setStatut_payement($data['statut_payement'] ?? 'Valider');
        $paiement->setCreated_at(new \DateTime());
        $paiement->setOrder_id($commande->getId());

        $this->entityManager->persist($paiement);
        
        // Mettre à jour la commande avec la relation Payement
        $commande->setPayement($paiement);
        
        if ($paiement->getStatut_payement() === 'Valider') {
            $commande->setState_order('En_cours');
        }

        $this->entityManager->flush();

        return $paiement;
    }

    /**
     * Valider un paiement
     */
    public function validerPaiement(Payement $paiement): void
    {
        $paiement->setStatut_payement('VALIDE');
        
        // Mettre à jour la commande
        if ($paiement->getOrder_id()) {
            $commande = $this->entityManager->getRepository(OrderTable::class)->find($paiement->getOrder_id());
            if ($commande) {
                $commande->setState_order('En_cours');
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Vérifier si une commande est payée
     */
    public function estPayee(OrderTable $commande): bool
    {
        if (!$commande->getPayement()) {
            return false;
        }

        $paiement = $this->entityManager->getRepository(Payement::class)->find($commande->getPayement()->getId());
        
        return $paiement && $paiement->getStatut_payement() === 'VALIDE';
    }
}