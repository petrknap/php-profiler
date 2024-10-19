<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PHPUnit\Framework\TestCase;

final class NullProfilerTest extends TestCase
{
    public function testCallsCallable(): void
    {
        $profiler = new NullProfiler();
        $callableWasCalled = false;

        $profiler->profile(static function () use (&$callableWasCalled): void {
            $callableWasCalled = true;
        });

        self::assertTrue($callableWasCalled);
    }

    public function testProfileReturnsCallablesOutput(): void
    {
        $profiler = new NullProfiler();

        $output = $profiler->profile(static fn (): string => 'output')->getOutput();

        self::assertSame('output', $output);
    }

    public function testProfileDoesNotRunProcessorAndReturnsCallablesOutput(): void
    {
        $profiler = new NullProfiler();
        $processorWasCalled = false;

        $output = $profiler->profile(static fn (): string => 'output')->process(static function () use (&$processorWasCalled): void {
            $processorWasCalled = true;
        });

        self::assertFalse($processorWasCalled);
        self::assertSame('output', $output);
    }

    public function testSnapshotDoesNotThrow(): void
    {
        $profiler = new NullProfiler();

        $profiler->snapshot();

        self::assertTrue(true);
    }
}
