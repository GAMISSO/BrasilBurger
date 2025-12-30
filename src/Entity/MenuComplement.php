<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'menu_complement')]
class MenuComplement
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $menu_id;

    public function getMenu_id(): int {
        return $this->menu_id;
    }

    public function setMenu_id(int $menu_id): self {
        $this->menu_id = $menu_id;
        return $this;
    }

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $complement_id;

    public function getComplement_id(): int {
        return $this->complement_id;
    }

    public function setComplement_id(int $complement_id): self {
        $this->complement_id = $complement_id;
        return $this;
    }

}
