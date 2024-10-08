<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/**
 * @note it is recommended to use {@see ProfilerInterface}
 */
final class Profiling
{
    private bool $wasFinished = false;

    /**
     * @param Profile<void> $profile
     */
    private function __construct(
        private readonly Profile $profile,
    ) {
    }

    public static function start(): self
    {
        $profile = new Profile();
        $profile->start();

        return new self($profile);
    }

    /**
     * @throws Exception\ProfilingHasBeenAlreadyFinished
     */
    public function finish(): ProfileInterface
    {
        if ($this->wasFinished) {
            throw new Exception\ProfilingHasBeenAlreadyFinished();
        }

        $this->profile->finish();
        $this->wasFinished = true;

        return $this->profile;
    }
}
