<?php

// src/DTO/RapportDTO.php
namespace App\DTO;

class RapportDTO
{
    public \DateTime $dateDebut;

    public \DateTime $dateFin;

    public string $type; // 'journalier', 'hebdomadaire', 'mensuel'

    public array $statistiques = [];

    public array $details = [];

    public ?\DateTime $dateGeneration = null;

    public function __construct(string $type, \DateTime $dateDebut, \DateTime $dateFin)
    {
        $this->type = $type;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->dateGeneration = new \DateTime();
    }

    public function ajouterStatistique(string $cle, $valeur): self
    {
        $this->statistiques[$cle] = $valeur;
        return $this;
    }

    public function ajouterDetail(string $section, array $data): self
    {
        if (!isset($this->details[$section])) {
            $this->details[$section] = [];
        }
        $this->details[$section][] = $data;
        return $this;
    }

    public function getPeriode(): string
    {
        return sprintf(
            "Du %s au %s",
            $this->dateDebut->format('d/m/Y'),
            $this->dateFin->format('d/m/Y')
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'periode' => $this->getPeriode(),
            'date_debut' => $this->dateDebut->format('Y-m-d'),
            'date_fin' => $this->dateFin->format('Y-m-d'),
            'date_generation' => $this->dateGeneration?->format('Y-m-d H:i:s'),
            'statistiques' => $this->statistiques,
            'details' => $this->details,
        ];
    }
}