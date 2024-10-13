<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PHPUnit\Framework\TestCase;

/**
 * @see ProfileInterface
 *
 * @mixin ProfileTest
 */
trait ProfileInterfaceTestTrait
{
    public function testProfilesDuration(): void
    {
        $profile = new Profile();

        $profile->start();
        sleep(2);
        $profile->finish();

        self::assertEquals(2, round($profile->getDuration()));
    }

    public function testProfilesMemoryUsageChange(): void
    {
        $profile = new Profile();

        $profile->start();
        $data = str_repeat('.', 4 * 1024 * 1024);
        $profile->finish();

        self::assertNotEmpty($data);
        self::assertEquals(4, round($profile->getMemoryUsageChange() / 1024 / 1024));
    }

    public function testProfilesMemoryUsages(): void
    {
        $parent = new Profile();
        $child = self::createMock(ProfileInterface::class);
        $child->expects(self::any())->method('getMemoryUsages')->willReturn([
            '1728646405.699651' => 2,
            '1728646405.699640' => 1,
            '1728646405.699689' => 3,
        ]);

        $parent->start();
        $parent->addChild($child);
        $parent->finish();
        $memoryUsages = $parent->getMemoryUsages();

        self::assertCount(2 + 3, $memoryUsages);
        self::assertEquals(1, array_shift($memoryUsages));
        self::assertEquals(2, array_shift($memoryUsages));
        self::assertEquals(3, array_shift($memoryUsages));
    }

    public function testRecordsDataOfCustomType(): void
    {
        $profile = new Profile();
        $profile->addRecord('a', 'a1');
        $profile->addRecord('b', 'b1');
        $profile->addRecord('a', 'a2');

        self::assertSame(
            ['a1', 'a2'],
            array_values($profile->getRecords('a')),
        );
        self::assertSame(
            ['b1'],
            array_values($profile->getRecords('b')),
        );
    }
}
