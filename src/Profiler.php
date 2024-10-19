<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/* final */class Profiler implements ProfilerInterface
{
    /**
     * @param bool $snapshotOnTick if true, it will do snapshot on each tick
     */
    public function __construct(
        private readonly bool $snapshotOnTick = Profile::DO_NOT_SNAPSHOT_ON_TICK,
        /**
         * @deprecated
         * @todo remove it
         */
        private readonly bool $listenToTicks = Profile::DO_NOT_SNAPSHOT_ON_TICK,
    ) {
    }

    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
    {
        $profiling = Profiling::start($this->snapshotOnTick, $this->listenToTicks);
        $output = $callable(Profiling::createNestedProfiler($profiling));
        /** @var Profile<mixed> $profile */
        $profile = $profiling->finish();
        $profile->setOutput($output);

        return $profile; // @phpstan-ignore return.type
    }

    public function snapshot(): void
    {
        throw new Exception\ProfilerCouldNotSnapshotOutsideParentProfile();
    }
}
