<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_line')]
class OrderLine
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
    private ?int $order_id = null;

    public function getOrder_id(): ?int {
        return $this->order_id;
    }

    public function setOrder_id(?int $order_id): self {
        $this->order_id = $order_id;
        return $this;
    }

    #[ORM\Column(type: 'string')]
    private string $item_type;

    public function getItem_type(): string {
        return $this->item_type;
    }

    public function setItem_type(string $item_type): self {
        $this->item_type = $item_type;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $burger_id = null;

    public function getBurger_id(): ?int {
        return $this->burger_id;
    }

    public function setBurger_id(?int $burger_id): self {
        $this->burger_id = $burger_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $menu_id = null;

    public function getMenu_id(): ?int {
        return $this->menu_id;
    }

    public function setMenu_id(?int $menu_id): self {
        $this->menu_id = $menu_id;
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
