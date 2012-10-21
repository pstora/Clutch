<?php

namespace Clutch\Competition;

abstract class AbstractRound implements RoundInterface
{
    public function __toString()
    {
        return $this->getName();
    }

    public function getName()
    {
        return '';
    }

    public function fight($fighter)
    {
        $methodName = 'fight' . $this->getName() . 'Round';

        $fighter->$methodName();
    }
}
