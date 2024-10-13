<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/* final */class Profiler implements ProfilerInterface
{
    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
    {
        $profiling = Profiling::start();
        $output = $callable(Profiling::createNestedProfiler($profiling));
        /** @var Profile<mixed> $profile */
        $profile = $profiling->finish();
        $profile->setOutput($output);

        return $profile; // @phpstan-ignore return.type
    }

    public function record(string $type, mixed $data): void
    {
        throw new Exception\ProfilerCouldNotRecordData();
    }
}
