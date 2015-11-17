<?php

namespace Enhavo\Bundle\ContactBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EnhavoContactExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('enhavo_contact.contact.model', $config['contact']['model']);
        $container->setParameter('enhavo_contact.contact.form', $config['contact']['form']);
        $container->setParameter('enhavo_contact.contact.template.render', $config['contact']['template']['render']);
        $container->setParameter('enhavo_contact.contact.template.recipient', $config['contact']['template']['recipient']);
        $container->setParameter('enhavo_contact.contact.template.sender', $config['contact']['template']['sender']);
        $container->setParameter('enhavo_contact.contact.recipient', $config['contact']['recipient']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}