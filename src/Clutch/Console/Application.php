<?php

namespace Clutch\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Clutch\Command;
//use Clutch\Command\Helper\DialogHelper;
use Clutch\Clutch;
//use Clutch\Factory;
//use Clutch\IO\IOInterface;
//use Clutch\IO\ConsoleIO;
//use Clutch\Util\ErrorHandler;

class Application extends BaseApplication
{
    public function __construct()
    {
        if (function_exists('ini_set')) {
            ini_set('xdebug.show_exception_trace', false);
            ini_set('xdebug.scream', false);
        }

        parent::__construct('Clutch', Clutch::VERSION);
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\About();
        $commands[] = new Command\Fight();
        //$commands[] = new Command\ConfigCommand();
        //$commands[] = new Command\DependsCommand();
        //$commands[] = new Command\InitCommand();
        //$commands[] = new Command\InstallCommand();
        //$commands[] = new Command\CreateProjectCommand();
        //$commands[] = new Command\UpdateCommand();
        //$commands[] = new Command\SearchCommand();
        //$commands[] = new Command\ValidateCommand();
        //$commands[] = new Command\ShowCommand();
        //$commands[] = new Command\RequireCommand();
        //$commands[] = new Command\DumpAutoloadCommand();
        //$commands[] = new Command\StatusCommand();
//
        //if ('phar:' === substr(__FILE__, 0, 5)) {
        //    $commands[] = new Command\SelfUpdateCommand();
        //}

        return $commands;
    }
}
