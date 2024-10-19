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
        private readonly bool $snapshotOnTick,
    ) {
    }

    /**
     * @param bool $snapshotOnTick if true, it will do snapshot on each tick
     */
    public static function start(
        bool $snapshotOnTick = Profile::DO_NOT_SNAPSHOT_ON_TICK,
        /**
         * @deprecated
         * @todo remove it
         */
        bool $listenToTicks = Profile::DO_NOT_SNAPSHOT_ON_TICK,
    ): self {
        $profile = new Profile($listenToTicks || $snapshotOnTick);
        $profile->start();

        return new self($profile, $listenToTicks || $snapshotOnTick);
    }

    /**
     * @throws Exception\ProfilingHasBeenAlreadyFinished
     */
    public function snapshot(): void
    {
        $this->checkProfileState();

        $this->profile->snapshot();
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
        return new class ($profiling->profile, $profiling->snapshotOnTick) extends Profiler {
            /**
             * @param Profile<mixed> $parentProfile
             */
            public function __construct(
                private readonly Profile $parentProfile,
                bool $snapshotOnTick,
            ) {
                parent::__construct(
                    snapshotOnTick: $snapshotOnTick,
                );
            }

            public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
            {
                if ($this->parentProfile->getState() !== ProfileState::Started) {
                    throw new Exception\ParentProfileIsNotStarted();
                }

                $this->parentProfile->unregisterTickSnapshot();
                try {
                    $profile = parent::profile($callable);
                    $this->parentProfile->addChild($profile);

                    return $profile;
                } finally {
                    $this->parentProfile->registerTickSnapshot();
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
