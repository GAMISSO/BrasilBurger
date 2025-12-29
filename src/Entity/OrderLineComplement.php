<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_line_complement')]
class OrderLineComplement
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
    private ?int $order_line_id = null;

    public function getOrder_line_id(): ?int {
        return $this->order_line_id;
    }

    public function setOrder_line_id(?int $order_line_id): self {
        $this->order_line_id = $order_line_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $complement_id = null;

    public function getComplement_id(): ?int {
        return $this->complement_id;
    }

    public function setComplement_id(?int $complement_id): self {
        $this->complement_id = $complement_id;
        return $this;
    }

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    #[ORM\Column(type: 'integer')]
    private int $prix;

    public function getPrix(): int {
        return $this->prix;
    }

    public function setPrix(int $prix): self {
        $this->prix = $prix;
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

}
