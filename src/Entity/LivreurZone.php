<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'livreur_zone')]
class LivreurZone
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Livreur::class)]
    #[ORM\JoinColumn(name: 'livreur_id', referencedColumnName: 'id')]
    private Livreur $livreur;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Zones::class)]
    #[ORM\JoinColumn(name: 'zone_id', referencedColumnName: 'id')]
    private Zones $zone;

    public function getLivreur(): Livreur {
        return $this->livreur;
    }

    public function setLivreur(Livreur $livreur): self {
        $this->livreur = $livreur;
        return $this;
    }

    public function getZone(): Zones {
        return $this->zone;
    }

    public function setZone(Zones $zone): self {
        $this->zone = $zone;
        return $this;
    }

}
