<?php

namespace Clutch\Competition;

abstract class AbstractFighter implements FighterInterface
{
    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getDescription();
    }

    public function getName()
    {
        return '';
    }

    public function getDescription()
    {
        return '';
    }
}
