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
            'short-term-profiling' => 'It took 0.0 s to do something.',
            'long-term-profiling' => '',
            'how-to-enable-disable-it' => 'It took 0.0 s to do something.' . 'something' . 'something',
            'cascade-profiling' => 'It took 0.0 s to do something.' . 'It took 0.0 s to do something before something and something, there are 1 children profiles.' . 'something',
            'tick-listening' => 'There are 3 memory usage records.',
        ];
    }
}
