<?php
namespace App\Service;

use App\Entity\OrderTable;
use App\Entity\Payement;

interface PaiementService
{
    public function enregistrerPaiement(OrderTable $commande, array $data): Payement;
    public function validerPaiement(Payement $paiement): void;
    public function estPayee(OrderTable $commande): bool;
}