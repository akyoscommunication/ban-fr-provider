<?php

namespace Akyos\BANFrProvider\Factory;

use App\Geocoder\Provider\GeoPFProvider;
use Bazinga\GeocoderBundle\ProviderFactory\AbstractFactory;
use Geocoder\Provider\Provider;
use Psr\Http\Client\ClientInterface;

class GeoBANFrFactory extends AbstractFactory
{
    public function __construct(?ClientInterface $httpClient = null)
    {
        parent::__construct($httpClient);
    }

    protected function getProvider(array $config): Provider
    {
        return new GeoPFProvider($this->httpClient);
    }
}
