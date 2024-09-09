<?php


namespace Kikwik\PageBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class KikwikPageBundle extends AbstractBundle
{

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $myConfigs = $builder->getExtensionConfig('kikwik_page');
        $pageEntityClass            = $myConfigs[0]['resolve_target_entities']['page']              ?? null;
        $pageTranslationEntityClass = $myConfigs[0]['resolve_target_entities']['page_translation']  ?? null;
        $blockEntityClass           = $myConfigs[0]['resolve_target_entities']['block']             ?? null;

        if($pageEntityClass && $pageTranslationEntityClass && $blockEntityClass)
        {
            // configure doctrine.orm.resolve_target_entities
            $doctrineConfig = [];
            $doctrineConfig['orm']['resolve_target_entities']['Kikwik\PageBundle\Model\PageInterface'] = $pageEntityClass;
            $doctrineConfig['orm']['resolve_target_entities']['Kikwik\PageBundle\Model\PageTranslationInterface'] = $pageTranslationEntityClass;
            $doctrineConfig['orm']['resolve_target_entities']['Kikwik\PageBundle\Model\BlockInterface'] = $blockEntityClass;
            $builder->prependExtensionConfig('doctrine', $doctrineConfig);

            // configure other bundles
            $container->import('../config/packages/cmf_routing.yaml');
            $container->import('../config/packages/stof_doctrine_extensions.yaml');
            $container->import('../config/packages/twig_component.yaml');
        }
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('admin_role')->defaultValue('ROLE_ADMIN_PAGE')->end()
                ->scalarNode('default_locale')->defaultValue('%kernel.default_locale%')->end()
                ->scalarNode('enabled_locales')->defaultValue('%kernel.enabled_locales%')->end()
                ->arrayNode('resolve_target_entities')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('page')->defaultNull()->end()
                        ->scalarNode('page_translation')->defaultNull()->end()
                        ->scalarNode('block')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $pageEntityClass            = $config['resolve_target_entities']['page'];
        $pageTranslationEntityClass = $config['resolve_target_entities']['page_translation'];
        $blockEntityClass           = $config['resolve_target_entities']['block'];

        if($pageEntityClass && $pageTranslationEntityClass && $blockEntityClass)
        {
            $builder->setParameter('kikwik_page.entity_class.page',$pageEntityClass);
            $builder->setParameter('kikwik_page.entity_class.page_translation',$pageTranslationEntityClass);
            $builder->setParameter('kikwik_page.entity_class.block',$blockEntityClass);
            $builder->setParameter('kikwik_page.enabled_locales',$config['enabled_locales']);

            $container->import('../config/services.xml');
            $container->import('../config/services_form.xml');
            $container->import('../config/services_repository.xml');

            $container->services()->get('kikwik_page.controller.admin_controller')
                ->arg('$enabledLocales', $config['enabled_locales'])
                ->arg('$adminRole', $config['admin_role'])
            ;

            $container->services()->get('kikwik_page.twig_components.admin_bar')
                ->arg('$adminRole', $config['admin_role'])
            ;
        }

    }

}