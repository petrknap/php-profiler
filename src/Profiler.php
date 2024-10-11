<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/* final */class Profiler implements ProfilerInterface
{
    /**
     * @var Profile<mixed>|null $parentProfile
     */
    protected Profile|null $parentProfile = null;

    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
    {
        $profiling = Profiling::start();
        $output = $callable($profiling->createNestedProfiler());
        /** @var Profile<mixed> $profile */
        $profile = $profiling->finish();
        $profile->setOutput($output);

        $this->parentProfile?->addChild($profile);

        return $profile; // @phpstan-ignore return.type
    }
}
