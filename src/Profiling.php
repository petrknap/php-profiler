<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

final class Profiling
{
    private bool $wasFinished = false;

    /**
     * @param Profile<mixed> $profile
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

    public function createNestedProfiler(): ProfilerInterface
    {
        return new class ($this->profile) extends Profiler {
            /**
             * @param Profile<mixed> $parentProfile
             */
            public function __construct(
                Profile $parentProfile,
            ) {
                $this->parentProfile = $parentProfile;
            }
        };
    }
}
