<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

interface ProfilerInterface
{
    /**
     * @template TOutput of mixed
     *
     * @param callable(self): TOutput $callable
     *
     * @return ProcessableProfileInterface<TOutput> & ProfileWithOutputInterface<TOutput>
     */
    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface;
}
