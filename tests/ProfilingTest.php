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

    public function testAddsSnapshotToProfile(): void
    {
        $profiling = Profiling::start();
        $profiling->snapshot();
        $profile = $profiling->finish();

        self::assertCount(2 + 1, $profile->getMemoryUsages());
    }

    public function testSnapshotThrowsOnFinishedProfile(): void
    {
        $profiling = Profiling::start();
        $profiling->finish();

        self::expectException(Exception\ProfilingHasBeenAlreadyFinished::class);

        $profiling->snapshot();
    }

    public function testFinishThrowsOnFinishedProfile(): void
    {
        $profiling = Profiling::start();
        $profiling->finish();

        self::expectException(Exception\ProfilingHasBeenAlreadyFinished::class);

        $profiling->finish();
    }
}
