<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

use PetrKnap\Shorts\PhpUnit\MarkdownFileTestInterface;
use PetrKnap\Shorts\PhpUnit\MarkdownFileTestTrait;
use PHPUnit\Framework\TestCase;

final class ReadmeTest extends TestCase implements MarkdownFileTestInterface
{
    use MarkdownFileTestTrait;

    public static function getPathToMarkdownFile(): string
    {
        return __DIR__ . '/../README.md';
    }

    public static function getExpectedOutputsOfPhpExamples(): iterable
    {
        return [
            'basic-profiling' => 'It took 0.0 s to do something.',
            'complex-profiling' => '',
            'how-to-enable-disable-it' => 'It took 0.0 s to do something.' . 'something' . 'something',
            'touching-profile' => 'There are 3 memory usage records.',
            'tick-listening' => 'There are 3 memory usage records.',
            'cascade-profiling' => 'There are 4 memory usage records.',
        ];
    }
}
