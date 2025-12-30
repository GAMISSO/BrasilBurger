<?php

// src/DTO/StatistiqueDTO.php
namespace App\DTO;

class StatistiqueDTO
{
    public int $commandesEnCours = 0;

    public int $commandesValidees = 0;

    public float $recettesJournalieres = 0;

    public int $commandesAnnulees = 0;

    public array $produitsPopulaires = [];

    public \DateTime $date;

    public function __construct(?\DateTime $date = null)
    {
        $this->date = $date ?? new \DateTime();
    }

    public function setCommandesEnCours(int $count): self
    {
        $this->commandesEnCours = $count;
        return $this;
    }

    public function setCommandesValidees(int $count): self
    {
        $this->commandesValidees = $count;
        return $this;
    }

    public function setRecettesJournalieres(float $montant): self
    {
        $this->recettesJournalieres = $montant;
        return $this;
    }

    public function setCommandesAnnulees(int $count): self
    {
        $this->commandesAnnulees = $count;
        return $this;
    }

    public function setProduitsPopulaires(array $produits): self
    {
        $this->produitsPopulaires = $produits;
        return $this;
    }

    public function getTotalCommandes(): int
    {
        return $this->commandesEnCours + $this->commandesValidees + $this->commandesAnnulees;
    }

    public function getTauxAnnulation(): float
    {
        $total = $this->getTotalCommandes();
        return $total > 0 ? ($this->commandesAnnulees / $total) * 100 : 0;
    }

    public function getRecettesMoyennes(): float
    {
        $commandesPayees = $this->commandesValidees;
        return $commandesPayees > 0 ? $this->recettesJournalieres / $commandesPayees : 0;
    }

    /**
     * Exporter en tableau
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'commandes_en_cours' => $this->commandesEnCours,
            'commandes_validees' => $this->commandesValidees,
            'recettes_journalieres' => $this->recettesJournalieres,
            'commandes_annulees' => $this->commandesAnnulees,
            'produits_populaires' => $this->produitsPopulaires,
            'total_commandes' => $this->getTotalCommandes(),
            'taux_annulation' => $this->getTauxAnnulation(),
            'recette_moyenne' => $this->getRecettesMoyennes(),
        ];
    }
}