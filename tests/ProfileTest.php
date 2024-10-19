<?php

declare(strict_types=1);
declare(ticks=1);

namespace PetrKnap\Profiler;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProfileTest extends TestCase
{
    use ProfileInterfaceTestTrait;
    use ProfileWithOutputInterfaceTestTrait;
    use ProcessableProfileInterfaceTestTrait;

    public function testAddsChildren(): void
    {
        $parent = new Profile();
        $child1 = self::createMock(ProfileInterface::class);
        $child2 = self::createMock(ProfileInterface::class);

        $parent->addChild($child1);
        $parent->addChild($child2);

        self::assertSame([$child1, $child2], $parent->getChildren());
    }

    #[DataProvider('dataTakesSnapshotOnTick')]
    public function testTakesSnapshotOnTick(bool|null $shouldItTakeSnapshot): void
    {
        $profile = $shouldItTakeSnapshot === null ? new Profile() : new Profile(takeSnapshotOnTick: $shouldItTakeSnapshot);
        $profile->start();
        for ($i = 0; $i < 5; $i++) {
            $i = (fn (int $i): int => $i)($i);
        }
        $profile->finish();

        self::assertCount($shouldItTakeSnapshot === true ? 9 : 2, $profile->getMemoryUsages());
    }

    public static function dataTakesSnapshotOnTick(): array
    {
        return [
            'default' => [null],
            'yes' => [true],
            'no' => [false],
        ];
    }
}
