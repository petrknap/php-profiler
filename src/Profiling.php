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

    /**
     * @internal should be used only by {@see Profiler::profile()}
     */
    public static function createNestedProfiler(Profiling $profiling): ProfilerInterface
    {
        return new class ($profiling->profile) extends Profiler {
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
