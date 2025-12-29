<?php

// src/DTO/LivraisonDTO.php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LivraisonDTO
{
    #[Assert\NotBlank]
    public array $commandeIds = [];

    #[Assert\NotBlank(message: "Le livreur est obligatoire")]
    #[Assert\Positive]
    public int $livreurId;

    public ?int $zoneId = null;

    public ?\DateTime $dateAffectation = null;

    public string $statut = 'AFFECTE';

    public ?string $notes = null;

    public function __construct()
    {
        $this->commandeIds = [];
        $this->dateAffectation = new \DateTime();
    }

    public function ajouterCommande(int $commandeId): self
    {
        if (!in_array($commandeId, $this->commandeIds)) {
            $this->commandeIds[] = $commandeId;
        }
        return $this;
    }

    public function getNombreCommandes(): int
    {
        return count($this->commandeIds);
    }
}