<?php

// src/DTO/CommandeFilterDTO.php
namespace App\DTO;

class OrderFilterDTO
{
    public ?string $stateOrder = null;

    public ?\DateTime $date = null;

    public ?int $clientId = null;

    public ?int $burgerId = null;

    public ?int $menuId = null;

    public ?string $typeLivraison = null;

    public ?int $zoneId = null;

    public int $page = 1;

    public int $limit = 20;

    public string $orderBy = 'created_at';

    public string $orderDirection = 'DESC';

    /**
     * Convertir en tableau pour la requête
     */
    public function toArray(): array
    {
        $filters = [];

        if ($this->stateOrder) {
            $filters['state_order'] = $this->stateOrder;
        }

        if ($this->date) {
            $filters['date'] = $this->date->format('Y-m-d');
        }

        if ($this->clientId) {
            $filters['client_id'] = $this->clientId;
        }

        if ($this->burgerId) {
            $filters['burger_id'] = $this->burgerId;
        }

        if ($this->menuId) {
            $filters['menu_id'] = $this->menuId;
        }

        if ($this->typeLivraison) {
            $filters['type_livraison'] = $this->typeLivraison;
        }

        if ($this->zoneId) {
            $filters['zone_id'] = $this->zoneId;
        }

        return $filters;
    }

    /**
     * Vérifier si des filtres sont actifs
     */
    public function hasFiltresActifs(): bool
    {
        return $this->stateOrder !== null 
            || $this->date !== null 
            || $this->clientId !== null 
            || $this->burgerId !== null 
            || $this->menuId !== null 
            || $this->typeLivraison !== null 
            || $this->zoneId !== null;
    }
}