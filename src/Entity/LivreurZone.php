<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'livreur_zone')]
class LivreurZone
{
    #[ORM\Column(type: 'integer')]
    private int $livreur_id;

    public function getLivreur_id(): int {
        return $this->livreur_id;
    }

    public function setLivreur_id(int $livreur_id): self {
        $this->livreur_id = $livreur_id;
        return $this;
    }

    #[ORM\Column(type: 'integer')]
    private int $zone_id;

    public function getZone_id(): int {
        return $this->zone_id;
    }

    public function setZone_id(int $zone_id): self {
        $this->zone_id = $zone_id;
        return $this;
    }

}
