<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_table')]
class OrderTable
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
    private string $state_order;

    public function getState_order(): string {
        return $this->state_order;
    }

    public function setState_order(string $state_order): self {
        $this->state_order = $state_order;
        return $this;
    }

    #[ORM\Column(type: 'string')]
    private string $type_livraison;

    public function getType_livraison(): string {
        return $this->type_livraison;
    }

    public function setType_livraison(string $type_livraison): self {
        $this->type_livraison = $type_livraison;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresse_livraison = null;

    public function getAdresse_livraison(): ?string {
        return $this->adresse_livraison;
    }

    public function setAdresse_livraison(?string $adresse_livraison): self {
        $this->adresse_livraison = $adresse_livraison;
        return $this;
    }

    #[ORM\Column(type: 'integer')]
    private int $total_prix;

    public function getTotal_prix(): int {
        return $this->total_prix;
    }

    public function setTotal_prix(int $total_prix): self {
        $this->total_prix = $total_prix;
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

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    public function getUpdated_at(): ?\DateTimeInterface {
        return $this->updated_at;
    }

    public function setUpdated_at(?\DateTimeInterface $updated_at): self {
        $this->updated_at = $updated_at;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $client_profil_id = null;

    public function getClient_profil_id(): ?int {
        return $this->client_profil_id;
    }

    public function setClient_profil_id(?int $client_profil_id): self {
        $this->client_profil_id = $client_profil_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $zone_id = null;

    public function getZone_id(): ?int {
        return $this->zone_id;
    }

    public function setZone_id(?int $zone_id): self {
        $this->zone_id = $zone_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $payement_id = null;

    public function getPayement_id(): ?int {
        return $this->payement_id;
    }

    public function setPayement_id(?int $payement_id): self {
        $this->payement_id = $payement_id;
        return $this;
    }

}
