<?php
// src/DTO/CommandeItemDTO.php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderLineDTO
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['BURGER', 'MENU'])]
    public string $itemType;

    public ?int $burgerId = null;

    public ?int $menuId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $quantity;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $prix;

    public array $complements = [];

    public function getSousTotal(): int
    {
        $total = $this->prix * $this->quantity;
        
        foreach ($this->complements as $complement) {
            $total += $complement['prix'] * $complement['quantite'];
        }
        
        return $total;
    }
}