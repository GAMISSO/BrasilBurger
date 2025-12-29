<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderDTO
{
    #[Assert\NotBlank(message: "Le type de livraison est obligatoire")]
    #[Assert\Choice(choices: ['Sur_place', 'A_retirer', 'A_livrer'])]
    public string $typeLivraison;

    public ?string $adresseLivraison = null;

    public ?int $zoneId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $clientProfilId;

    #[Assert\NotBlank]
    public array $items = [];

    public ?string $notes = null;

    public function __construct()
    {
        $this->items = [];
    }

    public function ajouterItem(OrderLineDTO $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function calculerTotal(): int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getSousTotal();
        }
        return $total;
    }
}