<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 23.10.17 13:26
 */

namespace Makm\GuestBundle\DependencyInjection\Compiler;

use Makm\GuestBundle\Security\AuthenticationProvider;
use Makm\GuestBundle\Security\Listener;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideAnonymousCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('makm_guest.override_anonymous_provider')) {
            $userProviderId = $container->getParameter('makm_guest.override_anonymous_provider');

            $definition = $container->getDefinition(Listener::class);
            $container->setDefinition('security.authentication.listener.anonymous', $definition);
            $definition = $container->getDefinition(AuthenticationProvider::class);
            $container->setDefinition('security.authentication.provider.anonymous', $definition);
            //set user provider
            $definition->setArgument(1, new ChildDefinition('security.user.provider.concrete.' . $userProviderId));
        }
    }
}
