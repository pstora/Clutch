<?php

namespace Clutch\Instrument;

class Chronometer implements InstrumentInterface
{
    protected $time_start;
    protected $time_end;

    public function getName()
    {
        return 'Chronometer (µs)';
    }

    public function start()
    {
        $this->time_start = microtime(true);
    }

    public function end()
    {
        $this->time_end = microtime(true);

        return $this->time_end - $this->time_start;
    }
}
