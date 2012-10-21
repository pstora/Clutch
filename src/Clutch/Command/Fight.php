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
    protected $competitionResult = array();

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
        $this->output = $output;

        $competition = $input->getArgument('competition');

        $competition = explode('/', $competition);
        $competitionClass = $competition[0] . '\\Competitions\\' . $competition[1] . '\\Competition';
        $competition = new $competitionClass;

        $this->output->writeln('Welcome to the <info>' . $competition . '</info> competition !');

        $this->output->writeln('Here are the competitors who will fight each others:');
        foreach ($competition->getFighters() as $fighter) {
            $this->output->writeln('     - <info>' . $fighter . '</info>');
        }

        $this->output->writeln('');

        $roundNumber = 0;
        foreach ($competition->getRounds() as $round) {
            $roundNumber++;
            $this->output->writeln('✭ <comment>Round ' . $roundNumber . '</comment>: <info>' . $round . '</info> - Ready ? Fight !');

            foreach ($competition->getFighters() as $fighter) {
                $results = $this->getFightResults($competition, $round, $fighter);

                $this->updateCompetitionResult($results, $round, $fighter);
            }
            $this->outputRoundResult($round);
            $this->output->writeln('');
        }

        $this->output->writeln('Thank you for watching us !');
    }

    private function getFightResults(CompetitionInterface $competition, RoundInterface $round, FighterInterface $fighter)
    {
        $results = array();

        $iteration = $competition->getIteration();

        $fightFile = 'php ' . $this->generateFight($competition, $round, $fighter);

        if (OutputInterface::VERBOSITY_VERBOSE === $this->output->getVerbosity()) {
            $this->output->write($fighter->getName());
        }
        for ($i = 0; $i < $iteration; $i++) {
            if (OutputInterface::VERBOSITY_VERBOSE === $this->output->getVerbosity()) {
                $this->output->write('.');
            }

            $outputResult = $this->getFightProcessResult($fightFile);

            $results = $this->addResults($results, $i, $outputResult);

            unset($outputResult);
        }
        if (OutputInterface::VERBOSITY_VERBOSE === $this->output->getVerbosity()) {
            $this->output->writeln('');
        }

        return $results;
    }

    private function addResults($results, $iteration, $outputResult)
    {
        foreach ($outputResult as $outputData) {
            $instrumentData = explode(":", $outputData);
            $results[$instrumentData[0]][$iteration] = $instrumentData[1];
        }

        return $results;
    }

    private function updateCompetitionResult($results, RoundInterface $round, FighterInterface $fighter)
    {
        foreach ($results as $instrument => $result) {
            $this->competitionResult[$round->getName()][$instrument]['value'][$fighter->getName()] = array_sum($result)/count($result);
            $this->competitionResult[$round->getName()][$instrument]['±'][$fighter->getName()] = ((max($result) - min($result))/2);
        }
    }

    private function outputRoundResult(RoundInterface $round)
    {
        $roundResult = $this->competitionResult[$round->getName()];
        $this->output->writeln('     And the winner is...');
        foreach ($roundResult as $instrument => $data) {
            $this->output->writeln('     <info>' . $instrument . '</info>:');
            $ranking = $data['value'];
            asort($ranking);
            $i = 0;
            foreach ($ranking as $fighter => $score) {
                $i++;
                $this->output->writeln('          ' . $i . '. <info>' . $fighter . '</info> with <comment>' . $score . '</comment> (±' . $data['±'][$fighter] . ')');
            }
        }
    }

    private function generateFight(CompetitionInterface $competition, RoundInterface $round, FighterInterface $fighter)
    {
        $filename = getcwd() . '/competitions/cache/' . $competition->getName() . '_' . $round->getName() . 'Round_' . $fighter->getName() . 'Fighter.php';

        if (file_exists($filename)) {
            unlink($filename);
        }
        file_put_contents($filename, $this->getFightContent(get_class($competition), get_class($round), get_class($fighter)));

        return $filename;
    }

    private function getFightContent($competitionClass, $roundClass, $fighterClass)
    {
        return <<<EOF
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
    }

    private function getFightProcessResult($fightFile)
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
