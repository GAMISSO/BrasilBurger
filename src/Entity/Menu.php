<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'menu')]
class Menu
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $burger_id = null;

    public function getBurger_id(): ?int {
        return $this->burger_id;
    }

    public function setBurger_id(?int $burger_id): self {
        $this->burger_id = $burger_id;
        return $this;
    }

    #[ORM\Column(type: 'integer')]
    private int $prix_total;

    public function getPrix_total(): int {
        return $this->prix_total;
    }

    public function setPrix_total(int $prix_total): self {
        $this->prix_total = $prix_total;
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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image = null;

    public function getImage(): ?string {
        return $this->image;
    }

    public function setImage(?string $image): self {
        $this->image = $image;
        return $this;
    }

}
