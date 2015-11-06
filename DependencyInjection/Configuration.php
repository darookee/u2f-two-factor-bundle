<?php

namespace R\U2FTwoFactorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ru2_f_two_factor');

        $rootNode
            ->children()
                ->scalarNode('formTemplate')->defaultValue('RU2FTwoFactorBundle:Authentication:form.html.twig')->end()
                ->scalarNode('registerTemplate')->defaultValue('RU2FTwoFactorBundle:Registration:register.html.twig')->end()
                ->scalarNode('authCodeParameter')->defaultValue('_auth_code')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
