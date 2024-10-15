<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

final class NullProfilerTest extends ProfilerTestCase
{
    public function testCallsCallable(): void
    {
        $callableWasCalled = false;

        self::getProfiler()->profile(static function () use (&$callableWasCalled): void {
            $callableWasCalled = true;
        });

        self::assertTrue($callableWasCalled);
    }

    public function testProfileReturnsCallablesOutput(): void
    {
        $output = self::getProfiler()->profile(static fn (): string => 'output')->getOutput();

        self::assertSame('output', $output);
    }

    public function testProfileDoesNotRunProcessorAndReturnsCallablesOutput(): void
    {
        $processorWasCalled = false;

        $output = self::getProfiler()->profile(static fn (): string => 'output')->process(static function () use (&$processorWasCalled): void {
            $processorWasCalled = true;
        });

        self::assertFalse($processorWasCalled);
        self::assertSame('output', $output);
    }

    protected static function getProfiler(): ProfilerInterface
    {
        return new NullProfiler();
    }

    protected static function getAllowedTimePerProfile(): float
    {
        return 0.000000001;
    }
}
