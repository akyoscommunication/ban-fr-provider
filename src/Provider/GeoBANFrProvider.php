<?php

namespace Akyos\BANFrProvider\Provider;

use Akyos\BANFrProvider\Model\GeoBANFrAddress;
use Geocoder\Collection;
use Geocoder\Exception\InvalidServerResponse;
use Geocoder\Http\Provider\AbstractHttpProvider;
use Geocoder\Model\AddressBuilder;
use Geocoder\Model\AddressCollection;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class GeoBANFrProvider extends AbstractHttpProvider
{
    const GEOPF_TYPE = 'type';
    private const GEOCODE_ENDPOINT_URL = 'https://api-adresse.data.gouv.fr/search/?q=%s&type=%s';
    private const REVERSE_ENDPOINT_URL = 'https://api-adresse.data.gouv.fr/search?lat=%F&lon=%F';

    public function __construct(
        ClientInterface $client,
    ){
        parent::__construct($client);
    }

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $address = $query->getText();
        $type = $query->getData(self::GEOPF_TYPE);
        $url = sprintf(self::GEOCODE_ENDPOINT_URL, urlencode($address), $type);

        $request = $this->getRequest($url);
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->parseResponse($response);
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        $coordinates = $query->getCoordinates();
        $url = sprintf(self::REVERSE_ENDPOINT_URL, $coordinates->getLatitude(), $coordinates->getLongitude());

        $request = $this->getRequest($url);
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->parseResponse($response);
    }

    public function getName(): string
    {
        return 'geopf_geocoder';
    }

    private function parseResponse(ResponseInterface $response): Collection
    {
        if ($response->getStatusCode() !== 200) {
            throw new InvalidServerResponse(sprintf('HTTP error %d', $response->getStatusCode()));
        }

        $content = $response->getBody()->getContents();
        $json = json_decode($content, true);

        if (isset($json['features']) && is_array($json['features'])) {
            $results = [];
            foreach ($json['features'] as $feature) {
                $builder = new AddressBuilder($this->getName());
                $this->defaultValues($builder);

                $properties = $feature['properties'];
                $geometry = $feature['geometry']['coordinates'];

                $builder->setCoordinates($geometry[1], $geometry[0]);

                foreach ($properties as $type => $values) {
                    $this->updateAddressComponent($builder, $type, $values);
                }

                /** @var GeoBANFrAddress $address */
                $address = $builder->build(GeoBANFrAddress::class);

                $address
                    ->setId($properties['id'])
                    ->setScore($properties['score'])
                ;

                $results[] = $address;
            }

            return new AddressCollection($results);
        }

        throw new InvalidServerResponse('Invalid response from GeoPF Geocoder');
    }

    private function updateAddressComponent(AddressBuilder $builder, string $type, $values)
    {
        switch ($type) {
            case 'name':
                $builder->setStreetName($values);
                break;
            case 'label':
                $builder->setValue('label', $values);
                break;
            case 'housenumber':
                $builder->setStreetNumber($values);
                break;
            case 'postcode':
                $builder->setPostalCode($values);
                break;
            case 'city':
                $builder->setLocality($values);
                break;
            case 'context':
                $values = explode(', ', $values);

                if (!empty($values[1])) {
                    $builder->addAdminLevel(1, $values[1], $values[0]);
                }

                if (!empty($values[2])) {
                    $builder->addAdminLevel(2, $values[2]);
                }
                break;
            default:
        }
    }

    private function defaultValues(AddressBuilder $builder)
    {
        $builder
            ->setCountry('France')
            ->setCountryCode('FR')
            ->setTimezone('Europe/Paris')
        ;
    }
}
