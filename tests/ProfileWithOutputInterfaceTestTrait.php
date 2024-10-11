<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PetrKnap\Optional\Exception\CouldNotGetValueOfEmptyOptional;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @see ProfileWithOutputInterface
 *
 * @mixin ProfileTest
 */
trait ProfileWithOutputInterfaceTestTrait
{
    #[DataProvider('dataReturnsOutput')]
    public function testReturnsOutput(string|null $output): void
    {
        $profile = new Profile();
        $profile->start();
        $profile->finish();
        $profile->setOutput($output);

        self::assertSame($output, $profile->getOutput());
    }

    public static function dataReturnsOutput(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'string' => ['string'],
        ];
    }

    /**
     * @note internal issue
     */
    public function testThrowsOnUnsetOutput(): void
    {
        $profile = new Profile();
        $profile->start();
        $profile->finish();

        self::expectException(CouldNotGetValueOfEmptyOptional::class);

        $profile->getOutput();
    }
}
