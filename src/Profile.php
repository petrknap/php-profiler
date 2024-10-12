<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PetrKnap\Optional\Optional;
use PetrKnap\Optional\OptionalFloat;
use PetrKnap\Optional\OptionalInt;

/**
 * @internal object can apply breaking changes within the same major version
 *
 * @template TOutput of mixed
 *
 * @implements ProcessableProfileInterface<TOutput>
 * @implements ProfileWithOutputInterface<TOutput>
 */
final class Profile implements ProcessableProfileInterface, ProfileWithOutputInterface
{
    private const MICROTIME_FORMAT = '%.6f';

    private ProfileState $state;
    private OptionalFloat $timeBefore;
    private OptionalInt $memoryUsageBefore;
    private OptionalFloat $timeAfter;
    private OptionalInt $memoryUsageAfter;
    /**
     * @var array<ProfileInterface>
     */
    private array $children = [];
    /**
     * @var Optional<Optional<TOutput|null>>
     */
    private Optional $outputOption;

    public function __construct()
    {
        $this->state = ProfileState::Created;
        $this->timeBefore = OptionalFloat::empty();
        $this->memoryUsageBefore = OptionalInt::empty();
        $this->timeAfter = OptionalFloat::empty();
        $this->memoryUsageAfter = OptionalInt::empty();
        $this->outputOption = Optional::empty();
    }

    /**
     * @throws Exception\ProfileCouldNotBeStarted
     */
    public function start(): void
    {
        Exception\ProfileCouldNotBeStarted::throwIf($this->state !== ProfileState::Created);

        $this->state = ProfileState::Started;
        $this->timeBefore = OptionalFloat::of(microtime(as_float: true));
        $this->memoryUsageBefore = OptionalInt::of(memory_get_usage(real_usage: true));
    }

    /**
     * @throws Exception\ProfileCouldNotBeFinished
     */
    public function finish(): void
    {
        Exception\ProfileCouldNotBeFinished::throwIf($this->state !== ProfileState::Started);

        $this->state = ProfileState::Finished;
        $this->timeAfter = OptionalFloat::of(microtime(as_float: true));
        $this->memoryUsageAfter = OptionalInt::of(memory_get_usage(real_usage: true));
    }

    /**
     * @throws Exception\ProfileCouldNotBeProcessed
     */
    public function process(callable $processor): mixed
    {
        Exception\ProfileCouldNotBeProcessed::throwIf($this->state !== ProfileState::Finished);

        $output = $this->getOutput();
        $processor($this);

        return $output;
    }

    /**
     * @param TOutput $output
     */
    public function setOutput(mixed $output): void
    {
        $this->outputOption = Optional::of(Optional::ofNullable($output));
    }

    public function getOutput(): mixed
    {
        /** @var TOutput */
        return $this->outputOption->orElseThrow()->orElse(null);
    }

    public function addChild(ProfileInterface $child): void
    {
        $this->children[] = $child;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getDuration(): float
    {
        return $this->timeAfter->orElseThrow() - $this->timeBefore->orElseThrow();
    }

    public function getMemoryUsageChange(): int
    {
        return $this->memoryUsageAfter->orElseThrow() - $this->memoryUsageBefore->orElseThrow();
    }

    public function getMemoryUsages(): array
    {
        $memoryUsages = [
            sprintf(self::MICROTIME_FORMAT, $this->timeBefore->orElseThrow()) => $this->memoryUsageBefore->orElseThrow(),
            sprintf(self::MICROTIME_FORMAT, $this->timeAfter->orElseThrow()) => $this->memoryUsageAfter->orElseThrow(),
        ];
        foreach ($this->children as $child) {
            $memoryUsages = array_merge(
                $memoryUsages,
                $child->getMemoryUsages(),
            );
        }

        ksort($memoryUsages);

        return $memoryUsages;
    }
}
