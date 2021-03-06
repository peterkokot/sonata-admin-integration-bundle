<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection\Factory;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class BlockAdminFactory implements AdminFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'block';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('extensions')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('block_cache')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('form_group')->defaultValue('form.group_metadata')->end()
                            ->scalarNode('form_tab')->defaultValue('form.tab_general')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('basepath')->defaultNull()->end()
            ->scalarNode('menu_basepath')->defaultNull()->end()
            ->enumNode('use_imagine')
                ->values([true, false, 'auto'])
                ->defaultValue('auto')
            ->end()
            ->enumNode('enable_menu')
                ->values([true, false, 'auto'])
                ->defaultValue('auto')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $container->setParameter('cmf_sonata_admin_integration.block.persistence.phpcr.basepath', $config['basepath']);
        $container->setParameter('cmf_sonata_admin_integration.block.persistence.phpcr.menu_basepath', $config['menu_basepath']);

        $container->setParameter('cmf_sonata_admin_integration.block.extension.block_cache.form_group', $config['extensions']['block_cache']['form_group']);
        $container->setParameter('cmf_sonata_admin_integration.block.extension.block_cache.form_tab', $config['extensions']['block_cache']['form_tab']);

        $loader->load('block.xml');

        $bundles = $container->getParameter('kernel.bundles');
        if (true === $config['use_imagine']
            || ('auto' === $config['use_imagine'] && isset($bundles['CmfMediaBundle']))
        ) {
            $loader->load('block-imagine.xml');
        }

        if (true === $config['enable_menu']
            || ('auto' === $config['enable_menu'] && isset($bundles['CmfMenuBundle']))
        ) {
            $loader->load('block-menu.xml');
        }
    }
}
