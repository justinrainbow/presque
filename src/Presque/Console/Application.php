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
use Presque\Command;
use Presque\Presque;

/**
 * Presque console application
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
class Application extends BaseApplication
{
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
        $this->registerCommands();

        return parent::doRun($input, $output);
    }

    /**
     * Initialize all the Presque commands
     */
    protected function registerCommands()
    {
        $this->addCommands(array(
            new Command\QueueJobCommand(),
            new Command\WorkerCommand()
        ));
    }
}