<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'zone_client_profil')]
class ZoneClientProfil
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Zones::class)]
    #[ORM\JoinColumn(name: 'zone_id', referencedColumnName: 'id')]
    private Zones $zone;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ClientProfil::class)]
    #[ORM\JoinColumn(name: 'client_profil_id', referencedColumnName: 'id')]
    private ClientProfil $clientProfil;

    public function getZone(): Zones {
        return $this->zone;
    }

    public function setZone(Zones $zone): self {
        $this->zone = $zone;
        return $this;
    }

    public function getClientProfil(): ClientProfil {
        return $this->clientProfil;
    }

    public function setClientProfil(ClientProfil $clientProfil): self {
        $this->clientProfil = $clientProfil;
        return $this;
    }

}
