<?php
namespace App\Service;

use App\Entity\OrderLine;
use App\Entity\OrderTable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface OrderService
{
    public function creerCommande(array $data): OrderTable;
    public function ajouterLigneCommande(OrderTable $commande, array $itemData): OrderLine;
    public function changerEtat(OrderTable $commande, string $nouvelEtat): void;
     public function annulerCommande(OrderTable $commande): void;
    public function terminerCommande(OrderTable $commande): void;
    public function peutEtreAnnulee(OrderTable $commande): bool;
    public function calculerMontantTotal(OrderTable $commande): int;
    public function getCommandesFilters(array $filters = []): array;

}