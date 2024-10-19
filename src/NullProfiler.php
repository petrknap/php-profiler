<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

final class NullProfiler implements ProfilerInterface
{
    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
    {
        return new class ($callable($this)) implements ProcessableProfileInterface, ProfileWithOutputInterface {
            public function __construct(
                private readonly mixed $output,
            ) {
            }

            public function process(callable $processor): mixed
            {
                return $this->output;
            }

            public function getOutput(): mixed
            {
                return $this->output;
            }

            public function getChildren(): array
            {
                return [];
            }

            public function getDuration(): float
            {
                return 0;
            }

            public function getMemoryUsageChange(): int
            {
                return 0;
            }

            public function getMemoryUsages(): array
            {
                return [];
            }
        };
    }

    public function takeSnapshot(): void
    {
    }
}
