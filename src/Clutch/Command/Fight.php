<?php

namespace Clutch\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Clutch\Competition\CompetitionInterface;
use Clutch\Competition\FighterInterface;
use Clutch\Competition\RoundInterface;

class Fight extends Command
{
    protected function configure()
    {
        $this
            ->setName('fight')
            ->setDescription('Are you ready to rumble ?')
            ->addArgument(
                'competition',
                InputArgument::REQUIRED,
                'The competition'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $competition = $input->getArgument('competition');

        $competition = explode('/', $competition);
        $competitionClass = $competition[0] . '\\Competitions\\' . $competition[1] . '\\Competition';
        $competition = new $competitionClass;

        $output->writeln('Welcome to the <info>' . $competition . '</info> competition !');

        $output->writeln('Here are the competitors who will fight each others:');
        foreach ($competition->getFighters() as $fighter) {
            $output->writeln('     - <info>' . $fighter . '</info>');
        }

        $output->writeln('');

        $roundNumber = 0;
        $iteration = $competition->getIteration();
        $competitionResult = array();
        foreach ($competition->getRounds() as $round) {
            $competitionResult[$round->getName()] = array();
            $roundNumber++;
            $output->writeln('✭ <comment>Round ' . $roundNumber . '</comment>: <info>' . $round . '</info> - Ready ? Fight !');

            foreach ($competition->getFighters() as $fighter) {
                $results = array();

                $fightFile = 'php ' . $this->generateFight($competition, $round, $fighter);

                if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                    $output->write($fighter->getName());
                }
                for ($i = 0; $i < $iteration; $i++) {
                    if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                        $output->write('.');
                    }

                    $outputResult = $this->getResult($fightFile);

                    foreach ($outputResult as $outputData) {
                        $instrumentData = explode(":", $outputData);
                        $results[$instrumentData[0]][$i] = $instrumentData[1];
                        unset($instrumentData);
                    }
                    unset($outputResult);
                }
                if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                    $output->writeln('');
                }

                foreach ($results as $instrument => $result) {
                    $competitionResult[$round->getName()][$instrument]['value'][$fighter->getName()] = array_sum($result)/count($result);
                    $competitionResult[$round->getName()][$instrument]['±'][$fighter->getName()] = ((max($result) - min($result))/2);
                }
            }
            $this->outputRoundResult($output, $competitionResult[$round->getName()]);
            $output->writeln('');
        }

        $output->writeln('Thank you for watching us !');
    }

    private function outputRoundResult($output, $roundResult)
    {
        $output->writeln('     And the winner is...');
        foreach ($roundResult as $instrument => $data) {
            $output->writeln('     <info>' . $instrument . '</info>:');
            $ranking = $data['value'];
            asort($ranking);
            $i = 0;
            foreach ($ranking as $fighter => $score) {
                $i++;
                $output->writeln('          ' . $i . '. <info>' . $fighter . '</info> with <comment>' . $score . '</comment> (±' . $data['±'][$fighter] . ')');
            }
        }
    }

    private function generateFight(CompetitionInterface $competition, RoundInterface $round, FighterInterface $fighter)
    {
        $competitionClass = get_class($competition);
        $roundClass = get_class($round);
        $fighterClass = get_class($fighter);

        $content = <<<EOF
<?php

require(__DIR__.'/../bootstrap.php');

\$competition = new
EOF
. ' ' . $competitionClass. <<<EOF
;
\$round = new
EOF
. ' ' . $roundClass. <<<EOF
;
\$fighter = new
EOF
. ' ' . $fighterClass. <<<EOF
;

\$result = \$competition->fight(\$fighter, \$round);

foreach (\$competition->getInstruments() as \$instrument) {
    print \$instrument->getName() . ':' . \$result[\$instrument->getName()] . "\\n";
}
EOF;

        $filename = getcwd() . '/competitions/cache/' . $competition->getName() . '_' . $round->getName() . 'Round_' . $fighter->getName() . 'Fighter.php';

        if (file_exists($filename)) {
            unlink($filename);
        }
        file_put_contents($filename, $content);

        return $filename;
    }

    private function getResult($fightFile)
    {
        $process = new Process($fightFile);

        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $outputResult = explode("\n", $process->getOutput());
        unset($outputResult[count($outputResult)-1]);

        return $outputResult;
    }
}
