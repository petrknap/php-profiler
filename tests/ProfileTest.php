<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

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
}
