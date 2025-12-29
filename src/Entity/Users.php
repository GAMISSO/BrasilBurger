<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class Users
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
    private string $password_hash;

    public function getPassword_hash(): string {
        return $this->password_hash;
    }

    public function setPassword_hash(string $password_hash): self {
        $this->password_hash = $password_hash;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 150)]
    private string $login;

    public function getLogin(): string {
        return $this->login;
    }

    public function setLogin(string $login): self {
        $this->login = $login;
        return $this;
    }

    #[ORM\Column(type: 'string')]
    private string $role_users;

    public function getRole_users(): string {
        return $this->role_users;
    }

    public function setRole_users(string $role_users): self {
        $this->role_users = $role_users;
        return $this;
    }

}
