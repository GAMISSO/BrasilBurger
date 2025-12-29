<?php

// src/DTO/PaiementDTO.php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaiementDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $orderId;

    #[Assert\NotBlank(message: "La méthode de paiement est obligatoire")]
    #[Assert\Choice(choices: ['WAVE', 'OM', 'ESPECES'])]
    public string $methodePaiement;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $montant;

    public ?string $transactionRef = null;

    public string $statutPaiement = 'En_cours';

    public ?\DateTime $datePaiement = null;

    public function __construct()
    {
        $this->datePaiement = new \DateTime();
    }

    public function estValide(): bool
    {
        return $this->statutPaiement === 'VALIDE';
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'methode_payement' => $this->methodePaiement,
            'montant' => $this->montant,
            'transaction_ref' => $this->transactionRef,
            'statut_payement' => $this->statutPaiement,
            'date_paiement' => $this->datePaiement?->format('Y-m-d H:i:s'),
        ];
    }
}