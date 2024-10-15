<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

final class ProfilerTest extends ProfilerTestCase
{
    public function testCallsCallable(): void
    {
        $callableWasCalled = false;

        self::getProfiler()->profile(static function () use (&$callableWasCalled) {
            $callableWasCalled = true;
        });

        self::assertTrue($callableWasCalled);
    }

    public function testProfilesCallable(): void
    {
        $profile = self::getProfiler()->profile(static fn ()  => sleep(1));

        self::assertEquals(1, round($profile->getDuration()));
    }

    public function testReturnsCallablesOutput(): void
    {
        $profile = self::getProfiler()->profile(static fn ()  => 'output');

        self::assertSame('output', $profile->getOutput());
    }

    protected static function getProfiler(): ProfilerInterface
    {
        return new Profiler();
    }

    protected static function getAllowedTimePerProfile(): float
    {
        return 0.005;
    }
}
