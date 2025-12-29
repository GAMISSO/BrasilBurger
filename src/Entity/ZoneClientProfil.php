<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'zone_client_profil')]
class ZoneClientProfil
{
    #[ORM\Column(type: 'integer')]
    private int $zone_id;

    public function getZone_id(): int {
        return $this->zone_id;
    }

    public function setZone_id(int $zone_id): self {
        $this->zone_id = $zone_id;
        return $this;
    }

    #[ORM\Column(type: 'integer')]
    private int $client_profil_id;

    public function getClient_profil_id(): int {
        return $this->client_profil_id;
    }

    public function setClient_profil_id(int $client_profil_id): self {
        $this->client_profil_id = $client_profil_id;
        return $this;
    }

}
