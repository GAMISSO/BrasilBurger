<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'delivery_assignment')]
class DeliveryAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: OrderTable::class)]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    private OrderTable $order;

    #[ORM\ManyToOne(targetEntity: Livreur::class)]
    #[ORM\JoinColumn(name: 'livreur_id', referencedColumnName: 'id')]
    private Livreur $livreur;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $assigned_at;

    #[ORM\Column(type: 'string')]
    private string $status;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getOrder(): OrderTable {
        return $this->order;
    }

    public function setOrder(OrderTable $order): self {
        $this->order = $order;
        return $this;
    }

    public function getLivreur(): Livreur {
        return $this->livreur;
    }

    public function setLivreur(Livreur $livreur): self {
        $this->livreur = $livreur;
        return $this;
    }

    public function getAssigned_at(): \DateTimeInterface {
        return $this->assigned_at;
    }

    public function setAssigned_at(\DateTimeInterface $assigned_at): self {
        $this->assigned_at = $assigned_at;
        return $this;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): self {
        $this->status = $status;
        return $this;
    }

}
