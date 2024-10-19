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

    public function testTakesSnapshotOnProfile(): void
    {
        $profiling = Profiling::start();
        $profiling->takeSnapshot();
        $profile = $profiling->finish();

        self::assertCount(2 + 1, $profile->getMemoryUsages());
    }

    public function testTakeSnapshotThrowsOnFinishedProfile(): void
    {
        $profiling = Profiling::start();
        $profiling->finish();

        self::expectException(Exception\ProfilingHasBeenAlreadyFinished::class);

        $profiling->takeSnapshot();
    }

    public function testFinishThrowsOnFinishedProfile(): void
    {
        $profiling = Profiling::start();
        $profiling->finish();

        self::expectException(Exception\ProfilingHasBeenAlreadyFinished::class);

        $profiling->finish();
    }
}
