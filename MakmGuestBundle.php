<?php

namespace Makm\GuestBundle;

use Makm\GuestBundle\DependencyInjection\Compiler\OverrideAnonymousCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MakmGuestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideAnonymousCompilerPass());
    }
}
