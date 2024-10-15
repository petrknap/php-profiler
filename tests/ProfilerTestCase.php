<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PHPUnit\Framework\TestCase;

abstract class ProfilerTestCase extends TestCase
{
    public function testPerformanceIsOk(): void
    {
        $profileCounter = 1;
        $callable = function (ProfilerInterface $profiler) use (&$callable, &$profileCounter) {
            if ($profileCounter++ < 1000) {
                $profiler->profile($callable);
            }
        };

        $profile = static::getProfiler()->profile($callable);

        self::assertSame(1001, $profileCounter);
        self::assertLessThanOrEqual(
            static::getAllowedTimePerProfile() * 1001,
            $profile->getDuration(),
        );
    }

    abstract protected static function getProfiler(): ProfilerInterface;

    /**
     * @return float seconds
     */
    abstract protected static function getAllowedTimePerProfile(): float;
}
