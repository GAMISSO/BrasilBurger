<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'livreur')]
class Livreur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 150)]
    private string $nom;

    public function getNom(): string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string')]
    private string $disponibilite;

    public function getDisponibilite(): string {
        return $this->disponibilite;
    }

    public function setDisponibilite(string $disponibilite): self {
        $this->disponibilite = $disponibilite;
        return $this;
    }

}
