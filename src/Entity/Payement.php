<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payement')]
class Payement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string')]
    private string $methode_payement;

    public function getMethode_payement(): string {
        return $this->methode_payement;
    }

    public function setMethode_payement(string $methode_payement): self {
        $this->methode_payement = $methode_payement;
        return $this;
    }

    #[ORM\Column(type: 'integer')]
    private int $montant;

    public function getMontant(): int {
        return $this->montant;
    }

    public function setMontant(int $montant): self {
        $this->montant = $montant;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $transaction_ref = null;

    public function getTransaction_ref(): ?string {
        return $this->transaction_ref;
    }

    public function setTransaction_ref(?string $transaction_ref): self {
        $this->transaction_ref = $transaction_ref;
        return $this;
    }

    #[ORM\Column(type: 'string')]
    private string $statut_payement;

    public function getStatut_payement(): string {
        return $this->statut_payement;
    }

    public function setStatut_payement(string $statut_payement): self {
        $this->statut_payement = $statut_payement;
        return $this;
    }

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $created_at;

    public function getCreated_at(): \DateTimeInterface {
        return $this->created_at;
    }

    public function setCreated_at(\DateTimeInterface $created_at): self {
        $this->created_at = $created_at;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $order_id = null;

    public function getOrder_id(): ?int {
        return $this->order_id;
    }

    public function setOrder_id(?int $order_id): self {
        $this->order_id = $order_id;
        return $this;
    }

}
