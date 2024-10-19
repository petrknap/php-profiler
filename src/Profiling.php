<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

final class Profiling
{
    /**
     * @param Profile<mixed> $profile
     */
    private function __construct(
        private readonly Profile $profile,
        private readonly bool $listenToTicks,
    ) {
    }

    public static function start(
        bool $listenToTicks = Profile::DO_NOT_LISTEN_TO_TICKS,
    ): self {
        $profile = new Profile($listenToTicks);
        $profile->start();

        return new self($profile, $listenToTicks);
    }

    /**
     * @throws Exception\ProfilingHasBeenAlreadyFinished
     */
    public function snapshot(): void
    {
        $this->checkProfileState();

        $this->profile->tickHandler();
    }

    /**
     * @throws Exception\ProfilingHasBeenAlreadyFinished
     */
    public function finish(): ProfileInterface
    {
        $this->checkProfileState();

        $this->profile->finish();

        return $this->profile;
    }

    /**
     * @internal should be used only by {@see Profiler::profile()}
     */
    public static function createNestedProfiler(Profiling $profiling): ProfilerInterface
    {
        return new class ($profiling->profile, $profiling->listenToTicks) extends Profiler {
            /**
             * @param Profile<mixed> $parentProfile
             */
            public function __construct(
                private readonly Profile $parentProfile,
                bool $listenToTicks,
            ) {
                parent::__construct(
                    listenToTicks: $listenToTicks,
                );
            }

            public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
            {
                if ($this->parentProfile->getState() !== ProfileState::Started) {
                    throw new Exception\ParentProfileIsNotStarted();
                }

                $this->parentProfile->unregisterTickHandler();
                try {
                    $profile = parent::profile($callable);
                    $this->parentProfile->addChild($profile);

                    return $profile;
                } finally {
                    $this->parentProfile->registerTickHandler();
                }
            }
        };
    }

    /**
     * @throws Exception\ProfilingHasBeenAlreadyFinished
     */
    private function checkProfileState(): void
    {
        if ($this->profile->getState() === ProfileState::Finished) {
            throw new Exception\ProfilingHasBeenAlreadyFinished();
        }
    }
}
