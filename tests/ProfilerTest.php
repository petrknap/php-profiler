<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PHPUnit\Framework\TestCase;

final class ProfilerTest extends TestCase
{
    public function testCallsCallable(): void
    {
        $profiler = new Profiler();
        $callableWasCalled = false;

        $profiler->profile(static function () use (&$callableWasCalled) {
            $callableWasCalled = true;
        });

        self::assertTrue($callableWasCalled);
    }

    public function testProfilesCallable(): void
    {
        $profiler = new Profiler();

        $profile = $profiler->profile(static fn ()  => sleep(1));

        self::assertEquals(1, round($profile->getDuration()));
    }

    public function testReturnsCallablesOutput(): void
    {
        $profiler = new Profiler();

        $profile = $profiler->profile(static fn ()  => 'output');

        self::assertSame('output', $profile->getOutput());
    }

    public function testSnapshotsOnParentProfile(): void
    {
        $profiler = new Profiler();

        $profile = $profiler->profile(static fn (ProfilerInterface $profiler) => $profiler->snapshot());

        self::assertCount(2 + 1, $profile->getMemoryUsages());
    }

    public function testSnapshotsThrowsOutsideProfile(): void
    {
        $profiler = new Profiler();

        self::expectException(Exception\ProfilerCouldNotSnapshotOutsideParentProfile::class);

        $profiler->snapshot();
    }
}
