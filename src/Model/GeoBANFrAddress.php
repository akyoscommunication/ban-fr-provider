<?php

namespace Akyos\BANFrProvider\Model;

use Geocoder\Model\Address;

class GeoBANFrAddress extends Address
{
    private $id;
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
