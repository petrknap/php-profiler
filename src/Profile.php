<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PetrKnap\Optional\Optional;
use PetrKnap\Optional\OptionalFloat;
use PetrKnap\Optional\OptionalInt;
use PetrKnap\Shorts\ArrayShorts;

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
    private const SORTED_BY_TIME = true;

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

    /**
     * @var array<string, array<numeric-string, mixed>>
     */
    private array $records = [];

    public function __construct()
    {
        $this->state = ProfileState::Created;
        $this->timeBefore = OptionalFloat::empty();
        $this->memoryUsageBefore = OptionalInt::empty();
        $this->timeAfter = OptionalFloat::empty();
        $this->memoryUsageAfter = OptionalInt::empty();
        $this->outputOption = Optional::empty();
    }

    public function getState(): ProfileState
    {
        return $this->state;
    }

    /**
     * @throws Exception\ProfileCouldNotBeStarted
     */
    public function start(): void
    {
        if ($this->state !== ProfileState::Created) {
            throw new Exception\ProfileCouldNotBeStarted();
        }

        $this->state = ProfileState::Started;
        $this->timeBefore = OptionalFloat::of(microtime(as_float: true));
        $this->memoryUsageBefore = OptionalInt::of(memory_get_usage(real_usage: true));
    }

    /**
     * @throws Exception\ProfileCouldNotBeFinished
     */
    public function finish(): void
    {
        if ($this->state !== ProfileState::Started) {
            throw new Exception\ProfileCouldNotBeFinished();
        }

        $this->state = ProfileState::Finished;
        $this->timeAfter = OptionalFloat::of(microtime(as_float: true));
        $this->memoryUsageAfter = OptionalInt::of(memory_get_usage(real_usage: true));
    }

    /**
     * @throws Exception\ProfileCouldNotBeProcessed
     */
    public function process(callable $processor): mixed
    {
        if ($this->state !== ProfileState::Finished) {
            throw new Exception\ProfileCouldNotBeProcessed();
        }

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

    public function getMemoryUsages(bool $sortedByTime = self::SORTED_BY_TIME): array
    {
        return self::expandRecords(
            [
                sprintf(self::MICROTIME_FORMAT, $this->timeBefore->orElseThrow()) => $this->memoryUsageBefore->orElseThrow(),
                sprintf(self::MICROTIME_FORMAT, $this->timeAfter->orElseThrow()) => $this->memoryUsageAfter->orElseThrow(),
            ],
            $this->children,
            __FUNCTION__,
            [false],
            sortedByKey: $sortedByTime,
        );
    }

    public function addRecord(string $type, mixed $data): void
    {
        $records = $this->records[$type] ?? [];
        $records[sprintf(self::MICROTIME_FORMAT, microtime(as_float: true))] = $data;
        $this->records[$type] = $records;
    }

    public function getRecords(string $type, bool $sortedByTime = self::SORTED_BY_TIME): array
    {
        return self::expandRecords(
            $this->records[$type] ?? [],
            $this->children,
            __FUNCTION__,
            [$type, false],
            sortedByKey: $sortedByTime,
        );
    }

    /**
     * @template TRecord of mixed
     *
     * @param array<numeric-string, TRecord> $myRecords
     * @param array<ProfileInterface> $myChildren
     * @param array<mixed> $args
     *
     * @return array<numeric-string, TRecord>
     */
    private static function expandRecords(
        array $myRecords,
        array $myChildren,
        string $__function__,
        array $args,
        bool $sortedByKey = false
    ): array {
        $expandedRecords = array_merge(
            $myRecords,
            ...array_map(
                static fn (ProfileInterface $child): array => call_user_func_array([$child, $__function__], $args), // @phpstan-ignore argument.type, return.type
                $myChildren,
            )
        );

        if ($sortedByKey) {
            ksort($expandedRecords);
        }

        return $expandedRecords;
    }
}
