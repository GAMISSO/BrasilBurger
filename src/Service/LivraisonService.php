<?php
namespace App\Service;

interface LivraisonService
{
    public function getCommandesParZone(): array;
    public function affecterLivreur(array $commandeIds, int $livreurId): void;
    public function getLivreursDisponibles(): array;
    public function getLivraisonsLivreur(int $livreurId): array;
    

}