<?php

namespace Akyos\BANFrProvider\Model;

use Geocoder\Model\Address;

class GeoPFAddress extends Address
{
    private $id;
    private $banId;
    private $score;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getBanId(): ?string
    {
        return $this->banId;
    }

    public function setBanId(?string $banId): static
    {
        $this->banId = $banId;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): static
    {
        $this->score = $score;

        return $this;
    }
}
