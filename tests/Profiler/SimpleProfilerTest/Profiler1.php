<?php

namespace PetrKnap\Php\Profiler\Test\SimpleProfilerTest;

use PetrKnap\Php\Profiler\Profile;
use PetrKnap\Php\Profiler\SimpleProfiler;

class Profiler1 extends SimpleProfiler
{
    /**
     * @var bool
     */
    protected static $enabled = false;

    /**
     * @var Profile[]
     */
    protected static $stack = [];
}
