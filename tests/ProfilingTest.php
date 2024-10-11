<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PHPUnit\Framework\TestCase;

final class ProfilingTest extends TestCase
{
    public function testProfiles(): void
    {
        $profiling = Profiling::start();
        sleep(1);
        $profile = $profiling->finish();

        self::assertEquals(1, round($profile->getDuration()));
    }

    public function testThrowsOnSecondFinishCall(): void
    {
        $profiling = Profiling::start();
        $profiling->finish();

        self::expectException(Exception\ProfilingHasBeenAlreadyFinished::class);

        $profiling->finish();
    }
}
