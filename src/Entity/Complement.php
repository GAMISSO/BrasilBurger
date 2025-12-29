<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'complement')]
class Complement
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

    #[ORM\Column(type: 'string', length: 255)]
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

    #[ORM\Column(type: 'string')]
    private string $type_complement;

    public function getType_complement(): string {
        return $this->type_complement;
    }

    public function setType_complement(string $type_complement): self {
        $this->type_complement = $type_complement;
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
