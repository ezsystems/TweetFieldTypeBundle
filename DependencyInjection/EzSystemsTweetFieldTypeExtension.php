<?php

namespace EzSystems\TweetFieldTypeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class EzSystemsTweetFieldTypeExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $configDirectoryPath = __DIR__.'/../Resources/config';

        $this->prependYamlConfigFile($container, 'ez_platformui', $configDirectoryPath.'/yui.yml');
        $this->prependYamlConfigFile($container, 'ezpublish', $configDirectoryPath.'/ez_field_templates.yml');
    }

    private function prependYamlConfigFile(ContainerBuilder $container, $extensionName, $configFilePath)
    {
        $config = Yaml::parse(file_get_contents($configFilePath));
        $container->prependExtensionConfig($extensionName, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('fieldtypes.yml');
        $loader->load('services.yml');
    }
}
