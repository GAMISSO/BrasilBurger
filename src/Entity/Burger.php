<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'burger')]
class Burger
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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image_url = null;

    public function getImage_url(): ?string {
        return $this->image_url;
    }

    public function setImage_url(?string $image_url): self {
        $this->image_url = $image_url;
        return $this;
    }

}
