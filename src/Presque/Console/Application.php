<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Presque\DependencyInjection\PresqueExtension;
use Presque\DependencyInjection\Configuration\Loader;
use Presque\Command;
use Presque\Presque;

/**
 * Presque console application
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
class Application extends BaseApplication
{
    protected $basePath;
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct('Presque', Presque::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

            new InputOption('--help',    '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--verbose', '-v', InputOption::VALUE_NONE, 'Increase verbosity of exceptions.'),
            new InputOption('--version', null, InputOption::VALUE_NONE, 'Display this behat version.'),
            new InputOption('--config',  '-c', InputOption::VALUE_REQUIRED, 'Specify config file to use.'),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->createContainer($input);

        $this->registerCommands();

        return parent::doRun($input, $output);
    }

    /**
     * Creates container instance, loads extensions and freezes it.
     *
     * @param InputInterface $input
     *
     * @return ContainerInterface
     */
    protected function createContainer(InputInterface $input)
    {
        $container = new ContainerBuilder();
        $this->loadCoreExtension($container, $this->loadConfiguration($container, $input));
        $container->compile();

        return $container;
    }

    /**
     * Configures container based on providen config file and profile.
     *
     * @param ContainerBuilder $container
     * @param InputInterface   $input
     */
    protected function loadConfiguration(ContainerBuilder $container, InputInterface $input)
    {
        // locate paths
        $this->basePath = getcwd();
        if ($configPath = $this->getConfigurationFilePath($input)) {
            $this->basePath = realpath(dirname($configPath));
        }

        // read configuration
        $loader  = new Loader($configPath);
        $profile = $input->getParameterOption(array('--profile', '-p')) ?: 'default';

        return $loader->loadConfiguration($profile);
    }

    /**
     * Finds configuration file and returns path to it.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getConfigurationFilePath(InputInterface $input)
    {
        // custom configuration file
        if ($file = $input->getParameterOption(array('--config', '-c'))) {
            if (is_file($file)) {
                return $file;
            }

            return;
        }

        // predefined config paths
        $cwd = rtrim(getcwd(), DIRECTORY_SEPARATOR);
        foreach (array_filter(array(
            $cwd.DIRECTORY_SEPARATOR.'presque.yml',
            $cwd.DIRECTORY_SEPARATOR.'presque.yml.dist',
            $cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'presque.yml',
            $cwd.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'presque.yml.dist',
        ), 'is_file') as $path) {
            return $path;
        }
    }

    /**
     * Loads core extension into container.
     *
     * @param ContainerBuilder $container
     * @param $array           $configs
     */
    protected function loadCoreExtension(ContainerBuilder $container, array $configs)
    {
        if (null === $this->basePath) {
            throw new RuntimeException(
                'Suite basepath is not set. Seems you have forgot to load configuration first.'
            );
        }

        $extension = new PresqueExtension($this->basePath);
        $extension->load($configs, $container);
        $container->addObjectResource($extension);
    }

    /**
     * Initialize all the Presque commands
     */
    protected function registerCommands()
    {
        $this->addCommands($this->container->resolveServices($this->container->getParameter('presque.commands')));
    }
}