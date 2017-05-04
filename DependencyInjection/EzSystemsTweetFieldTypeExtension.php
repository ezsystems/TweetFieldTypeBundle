<?php

namespace EzSystems\TweetFieldTypeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EzSystemsTweetFieldTypeExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/ez_field_templates.yml'));
        $container->prependExtensionConfig('ezpublish', $config);
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('fieldtypes.yml');
        $loader->load('services.yml');
    }
}
