<?php

namespace Clutch\Instrument;

interface InstrumentInterface
{
    public function getName();
    public function start();
    public function end();
}
