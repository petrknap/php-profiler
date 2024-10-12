<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PetrKnap\Optional\Exception\CouldNotGetValueOfEmptyOptional;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @see ProcessableProfileInterface
 *
 * @mixin ProfileTest
 */
trait ProcessableProfileInterfaceTestTrait
{
    #[DataProvider('dataReturnsOutput')]
    public function testRunsProcessorAndReturnsOutput(string|null $output): void
    {
        $profile = new Profile();
        $profile->start();
        $profile->finish();
        $profile->setOutput($output);

        $processorArgs = null;
        self::assertSame($output, $profile->process(static function () use (&$processorArgs) {
            $processorArgs = func_get_args();
        }));
        self::assertCount(1, $processorArgs);
        self::assertSame($profile, $processorArgs[0]);
    }

    /**
     * @note internal issue
     */
    #[DataProvider('dataThrowsOnUnprocessableProfile')]
    public function testThrowsOnUnprocessableProfile(ProcessableProfileInterface $unprocessableProfile, string $expectedException): void
    {
        self::expectException($expectedException);

        $unprocessableProfile->process(fn () => null);
    }

    public static function dataThrowsOnUnprocessableProfile(): array
    {
        $unfinished = new Profile();
        $unfinished->start();
        $outputless = new Profile();
        $outputless->start();
        $outputless->finish();
        return [
            'unstarted' => [new Profile(), Exception\ProfileCouldNotBeProcessed::class],
            'unfinished' => [$unfinished, Exception\ProfileCouldNotBeProcessed::class],
            'outputless' => [$outputless, CouldNotGetValueOfEmptyOptional::class],
        ];
    }

    public function testThrowsWhenProcessorThrows(): void
    {
        $profile = new Profile();
        $profile->start();
        $profile->finish();
        $profile->setOutput(null);
        $expectedException = new \Exception();

        self::expectExceptionObject($expectedException);

        $profile->process(fn() => throw $expectedException);
    }
}
