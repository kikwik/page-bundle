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
        // configure other bundles
        $container->import('../config/packages/cmf_routing.yaml');
        $container->import('../config/packages/stof_doctrine_extensions.yaml');
        $container->import('../config/packages/twig_component.yaml');

        // configure doctrine.orm.resolve_target_entities
        $myConfigs = $builder->getExtensionConfig('kikwik_page');
        $pageEntityClass            = $myConfigs[0]['resolve_target_entities']['page']              ?? 'Kikwik\PageBundle\Entity\Page';
        $pageTranslationEntityClass = $myConfigs[0]['resolve_target_entities']['page_translation']  ?? 'Kikwik\PageBundle\Entity\PageTranslation';
        $blockEntityClass           = $myConfigs[0]['resolve_target_entities']['block']             ?? 'Kikwik\PageBundle\Entity\Block';

        $doctrineConfig = [];
        $doctrineConfig['orm']['resolve_target_entities']['Kikwik\PageBundle\Model\PageInterface'] = $pageEntityClass;
        $doctrineConfig['orm']['resolve_target_entities']['Kikwik\PageBundle\Model\PageTranslationInterface'] = $pageTranslationEntityClass;
        $doctrineConfig['orm']['resolve_target_entities']['Kikwik\PageBundle\Model\BlockInterface'] = $blockEntityClass;
        $builder->prependExtensionConfig('doctrine', $doctrineConfig);
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
                        ->scalarNode('page')->defaultValue('Kikwik\PageBundle\Entity\Page')->end()
                        ->scalarNode('page_translation')->defaultValue('Kikwik\PageBundle\Entity\PageTranslation')->end()
                        ->scalarNode('block')->defaultValue('Kikwik\PageBundle\Entity\Block')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.xml');
        $container->import('../config/services_form.xml');
        $container->import('../config/services_repository.xml');

        $builder->setParameter('kikwik_page.entity_class.page',$config['resolve_target_entities']['page']);
        $builder->setParameter('kikwik_page.entity_class.page_translation',$config['resolve_target_entities']['page_translation']);
        $builder->setParameter('kikwik_page.entity_class.block',$config['resolve_target_entities']['block']);


        $container->services()
            ->get('kikwik_page.controller.admin_controller')
            ->arg('$enabledLocales', $config['enabled_locales'])
            ->arg('$adminRole', $config['admin_role'])
            ->arg('$pageClass', $config['resolve_target_entities']['page'])
            ->arg('$pageTranslationClass', $config['resolve_target_entities']['page_translation'])
        ;

        $container->services()
            ->get('kikwik_page.twig_components.admin_bar')
            ->arg('$adminRole', $config['admin_role'])
        ;
    }

}