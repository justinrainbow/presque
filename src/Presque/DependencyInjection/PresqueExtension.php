<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * PresqueExtension
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
class PresqueExtension implements ExtensionInterface
{
    /**
     * Responds to the presque configuration parameter.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/config'));
        $loader->load('presque.xml');

        $this->addCompilerPasses($container);
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/config/schema';
    }

    public function getNamespace()
    {
        return 'http://justinrainbow.com/schema/dic/presque';
    }

    public function getAlias()
    {
        return 'presque';
    }

    /**
     * Adds core compiler passes to container.
     *
     * @param ContainerBuilder $container
     */
    protected function addCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new Compiler\ConsoleCommandsPass());
    }
}