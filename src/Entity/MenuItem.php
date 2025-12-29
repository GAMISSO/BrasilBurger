<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'menu_item')]
class MenuItem
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $complement_id = null;

    public function getComplement_id(): ?int {
        return $this->complement_id;
    }

    public function setComplement_id(?int $complement_id): self {
        $this->complement_id = $complement_id;
        return $this;
    }

}
