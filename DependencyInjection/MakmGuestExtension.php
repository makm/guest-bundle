<?php

namespace Makm\GuestBundle\DependencyInjection;

use Makm\GuestBundle\Security\Listener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MakmGuestExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // id parameter of provider
        $container->setParameter('makm_guest.override_anonymous_provider', $config['override_anonymous_provider']);

        //add params of listener
        $definition = $container->getDefinition(Listener::class);
        $definition->replaceArgument(4, $config['remember_cookie']);

    }

}
