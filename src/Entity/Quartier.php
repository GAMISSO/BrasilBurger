<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'quartier')]
class Quartier
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
    private ?int $zone_id = null;

    public function getZone_id(): ?int {
        return $this->zone_id;
    }

    public function setZone_id(?int $zone_id): self {
        $this->zone_id = $zone_id;
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
