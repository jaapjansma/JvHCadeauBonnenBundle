<?php

namespace JvH\CadeauBonnenBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CadeauBonnenExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('jvh.cadeabonnen.grootboek_cadeabonnen', $config['grootboek_cadeabonnen']);
        $container->setParameter('jvh.cadeabonnen.verkoop_grootboek_cadeabonnen_nl', $config['verkoop_grootboek_cadeabonnen_nl']);
        $container->setParameter('jvh.cadeabonnen.verkoop_grootboek_cadeabonnen_eu', $config['verkoop_grootboek_cadeabonnen_eu']);
        $container->setParameter('jvh.cadeabonnen.verkoop_grootboek_cadeabonnen_wereld', $config['verkoop_grootboek_cadeabonnen_wereld']);
    }
}
