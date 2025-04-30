<?php

namespace Akyos\BANFrProvider\DependencyInjection;

use Akyos\BANFrProvider\Factory\GeoBANFrFactory;
use Akyos\BANFrProvider\Factory\GeoPFFactory;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BANFrProviderExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

//        if (isset($bundles['BazingaGeocoderBundle'])) {
//            $container->prependExtensionConfig('bazinga_geocoder', [
//                'providers' => [
//                    'geopf' => [
//                        'factory' => GeoPFFactory::class,
//                        'aliases' => [
//                            'geocoder.provider.geopf_geocoder'
//                        ]
//                    ],
//                    'BANFr' => [
//                        'factory' => GeoBANFrFactory::class,
//                        'aliases' => [
//                            'geocoder.provider.BANFr_geocoder'
//                        ]
//                    ],
//                ],
//            ]);
//        }
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        try {
            $loader->load('services.yaml');
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
