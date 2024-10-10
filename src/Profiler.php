<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/* final */class Profiler implements ProfilerInterface
{
    public function __construct(
        private readonly bool $listenToTicks = Profile::DO_NOT_LISTEN_TO_TICKS,
    ) {
    }

    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
    {
        $profiling = Profiling::start($this->listenToTicks);
        $output = $callable(Profiling::createNestedProfiler($profiling));
        /** @var Profile<mixed> $profile */
        $profile = $profiling->finish();
        $profile->setOutput($output);

        return $profile; // @phpstan-ignore return.type
    }
}
