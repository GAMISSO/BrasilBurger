<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'menu_complement')]
class MenuComplement
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(name: 'menu_id', referencedColumnName: 'id')]
    private Menu $menu;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Complement::class)]
    #[ORM\JoinColumn(name: 'complement_id', referencedColumnName: 'id')]
    private Complement $complement;

    public function getMenu(): Menu {
        return $this->menu;
    }

    public function setMenu(Menu $menu): self {
        $this->menu = $menu;
        return $this;
    }

    public function getComplement(): Complement {
        return $this->complement;
    }

    public function setComplement(Complement $complement): self {
        $this->complement = $complement;
        return $this;
    }

}
