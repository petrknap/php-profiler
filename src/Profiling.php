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
        private readonly bool $takeSnapshotOnTick,
    ) {
    }

    public static function start(
        bool $takeSnapshotOnTick = Profile::DO_NOT_TAKE_SNAPSHOT_ON_TICK,
        /**
         * @deprecated backward compatibility with old named argument calls
         *
         * @todo remove it
         */
        bool $listenToTicks = Profile::DO_NOT_TAKE_SNAPSHOT_ON_TICK,
    ): self {
        $profile = new Profile($listenToTicks || $takeSnapshotOnTick);
        $profile->start();

        return new self($profile, $listenToTicks || $takeSnapshotOnTick);
    }

    /**
     * @throws Exception\ProfilingHasBeenAlreadyFinished
     */
    public function takeSnapshot(): void
    {
        $this->checkProfileState();

        $this->profile->takeSnapshot();
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
        return new class ($profiling->profile, $profiling->takeSnapshotOnTick) extends Profiler {
            /**
             * @param Profile<mixed> $parentProfile
             */
            public function __construct(
                private readonly Profile $parentProfile,
                bool $takeSnapshotOnTick,
            ) {
                parent::__construct(
                    takeSnapshotOnTick: $takeSnapshotOnTick,
                );
            }

            public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
            {
                if ($this->parentProfile->getState() !== ProfileState::Started) {
                    throw new Exception\ParentProfileIsNotStarted();
                }

                $this->parentProfile->unregisterTickHandlers();
                try {
                    $profile = parent::profile($callable);
                    $this->parentProfile->addChild($profile);

                    return $profile;
                } finally {
                    $this->parentProfile->registerTickHandlers();
                }
            }

            public function takeSnapshot(): void
            {
                if ($this->parentProfile->getState() !== ProfileState::Started) {
                    throw new Exception\ProfilerCouldNotTakeSnapshotOutsideParentProfile();
                }
                $this->parentProfile->takeSnapshot();
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
