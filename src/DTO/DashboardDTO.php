<?php

// src/DTO/DashboardDTO.php
namespace App\DTO;

class DashboardDTO
{
    public StatistiqueDTO $statistiques;

    public array $commandesRecentes = [];

    public array $commandesEnCours = [];

    public array $commandesALivrer = [];

    public int $nombreLivreursDisponibles = 0;

    public array $alertes = [];

    public function __construct()
    {
        $this->statistiques = new StatistiqueDTO();
    }

    public function ajouterAlerte(string $type, string $message, string $niveau = 'info'): self
    {
        $this->alertes[] = [
            'type' => $type,
            'message' => $message,
            'niveau' => $niveau,
            'timestamp' => new \DateTime(),
        ];
        return $this;
    }

    public function hasAlertes(): bool
    {
        return !empty($this->alertes);
    }

    public function getAlertesParNiveau(string $niveau): array
    {
        return array_filter($this->alertes, fn($alerte) => $alerte['niveau'] === $niveau);
    }
}