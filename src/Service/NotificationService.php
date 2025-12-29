<?php
namespace App\Service;

interface NotificationService{
    public function envoyerNotification(string $type, array $data): void;
    public function notifierChangementEtat(int $commandeId, string $nouvelEtat): void;
    public function notifierNouvelleCommande(int $commandeId): void;
    public function notifierAffectationLivreur(int $livreurId, array $commandeIds): void;
}