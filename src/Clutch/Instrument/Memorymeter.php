<?php

namespace Clutch\Instrument;

class Memorymeter implements InstrumentInterface
{
    protected $memory_start;
    protected $memory_end;

    public function getName()
    {
        return 'Memorymeter (bytes)';
    }

    public function start()
    {
        $this->memory_start = memory_get_usage();
    }

    public function end()
    {
        $this->memory_end = memory_get_usage();

        return $this->memory_end - $this->memory_start;
    }
}
