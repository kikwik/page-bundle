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
        $builder->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Kikwik\PageBundle\Twig\Components\\' => [
                    'template_directory' => '@KikwikPage/components/',
                    'name_prefix' => 'KikwikPage',
                ],
            ],
        ]);

        $container->import('../config/packages/cmf_routing.yaml');
        $container->import('../config/packages/stof_doctrine_extensions.yaml');
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('admin_role')->defaultValue('ROLE_ADMIN_PAGE')->end()
                ->scalarNode('default_locale')->defaultValue('%kernel.default_locale%')->end()
                ->scalarNode('enabled_locales')->defaultValue('%kernel.enabled_locales%')->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.xml');

        $container->import('../config/services_block.xml');

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