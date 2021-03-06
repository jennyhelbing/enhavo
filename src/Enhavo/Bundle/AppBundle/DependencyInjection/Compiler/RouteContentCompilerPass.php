<?php

namespace Enhavo\Bundle\AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


class RouteContentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('enhavo_app.route_content_collector')) {
            return;
        }

        $definition = $container->getDefinition(
            'enhavo_app.route_content_collector'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'enhavo_route_content_loader'
        );

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'add',
                    array(new Reference($id))
                );
            }
        }
    }
} 