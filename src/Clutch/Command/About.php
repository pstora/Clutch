<?php

namespace Clutch\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class About extends Command
{
    protected function configure()
    {
        $this
            ->setName('about')
            ->setDescription('Short information about Clutch')
            ->setHelp(<<<EOT
<info>php bin/clutch about</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(<<<EOT
<info>Clutch</info>
<comment>Clutch can make fight several PHP code to know which is the best in under certain conditions.</comment>
EOT
        );

    }
}
