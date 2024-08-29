<?php


namespace Kikwik\PageBundle;

use Kikwik\PageBundle\Block\BlockComponentInterface;
use Kikwik\PageBundle\Repository\PageRepository;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class KikwikPageBundle extends AbstractBundle
{

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/cmf_routing.yaml');
        $container->import('../config/packages/stof_doctrine_extensions.yaml');
        $container->import('../config/packages/twig_component.yaml');
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('admin_role')->defaultValue('ROLE_ADMIN_PAGE')->end()
                ->scalarNode('default_locale')->defaultValue('%kernel.default_locale%')->end()
                ->scalarNode('enabled_locales')->defaultValue('%kernel.enabled_locales%')->end()
                ->booleanNode('enable_components')->defaultValue(false)->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.xml');

        if($config['enable_components'])
        {
            $container->import('../config/services_block.xml');

        }

        $container->services()
            ->get('kikwik_page.controller.admin_controller')
            ->arg('$enabledLocales', $config['enabled_locales'])
            ->arg('$adminRole', $config['admin_role'])
        ;


        $container->services()
            ->get('kikwik_page.twig_components.admin_bar')
            ->arg('$adminRole', $config['admin_role'])
        ;
    }


}