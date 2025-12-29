<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'delivery_assignment')]
class DeliveryAssignment
{
    #[ORM\Column(type: 'integer')]
    private int $id;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $livreur_id = null;

    public function getLivreur_id(): ?int {
        return $this->livreur_id;
    }

    public function setLivreur_id(?int $livreur_id): self {
        $this->livreur_id = $livreur_id;
        return $this;
    }

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $assigned_at;

    public function getAssigned_at(): \DateTimeInterface {
        return $this->assigned_at;
    }

    public function setAssigned_at(\DateTimeInterface $assigned_at): self {
        $this->assigned_at = $assigned_at;
        return $this;
    }

    #[ORM\Column(type: 'string')]
    private string $statut;

    public function getStatut(): string {
        return $this->statut;
    }

    public function setStatut(string $statut): self {
        $this->statut = $statut;
        return $this;
    }

}
