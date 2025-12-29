<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'client_profil')]
class ClientProfil
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

    #[ORM\Column(type: 'string', length: 150)]
    private string $nom;

    public function getNom(): string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 150)]
    private string $prenom;

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self {
        $this->prenom = $prenom;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self {
        $this->adresse = $adresse;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $telephone = null;

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self {
        $this->telephone = $telephone;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $email = null;

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): self {
        $this->email = $email;
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

    #[ORM\Column(type: 'integer')]
    private int $quartier_id;

    public function getQuartier_id(): int {
        return $this->quartier_id;
    }

    public function setQuartier_id(int $quartier_id): self {
        $this->quartier_id = $quartier_id;
        return $this;
    }

}
