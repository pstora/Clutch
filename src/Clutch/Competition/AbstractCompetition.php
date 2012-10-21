<?php

namespace Clutch\Competition;

use Clutch\Instrument\InstrumentInterface;

abstract class AbstractCompetition implements CompetitionInterface
{
    protected $instruments = array();
    protected $rounds = array();
    protected $fighters = array();

    public function __toString()
    {
        return $this->getName();
    }

    public function getName()
    {
        return '';
    }

    public function getDescription()
    {
        return '';
    }

    public function getIteration()
    {
        return 100;
    }

    public function getFighters()
    {
        return $this->fighters;
    }

    public function getInstruments()
    {
        return $this->instruments;
    }

    public function getRounds()
    {
        return $this->rounds;
    }

    public function addFighter(FighterInterface $fighter)
    {
        $this->fighters[] = $fighter;

        return $this;
    }

    public function addInstrument(InstrumentInterface $instrument)
    {
        $this->instruments[] = $instrument;

        return $this;
    }

    public function addRound(RoundInterface $round)
    {
        $this->rounds[] = $round;

        return $this;
    }

    public function fight(FighterInterface $fighter, RoundInterface $round)
    {
        $result = array();

        foreach ($this->getInstruments() as $instrument) {
            $instrument->start();
        }
        $round->fight($fighter);
        foreach ($this->getInstruments() as $instrument) {
            $result[$instrument->getName()] = $instrument->end();
        }

        return $result;
    }
}
