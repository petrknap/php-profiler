# PHP profiler for short-term & long-term profiling

This tool allows you to monitor performance and detect memory leaks as well as inconsistent performance behavior of your application over time.

## Basic profiling

For basic profiling you can use a profiling helper.
The [`Profiling`](./src/Profiling.php) will allow you to profile between `start` and `finish` methods calls.

```php
namespace PetrKnap\Profiler;

$profiling = Profiling::start();
// do something
$profile = $profiling->finish();

printf('It took %.1f s to do something.', $profile->getDuration());
```

The [`Profiling`](./src/Profiling.php) is simple and **cannot be turned on and off** easily.
So a [profiler](./src/ProfilerInterface.php) was created for the purpose of hard-coded more complex profiling.

## Complex profiling

Request a [profiler](./src/ProfilerInterface.php) as a dependency and call a `profile` method on it.

```php
namespace PetrKnap\Profiler;

function doSomething(ProfilerInterface $profiler): string {
    return $profiler->profile(function (): string {
        return 'something';
    })->process(fn (ProfileInterface $profile) => printf(
        'It took %.1f s to do something.',
        $profile->getDuration(),
    ));
}
```

### How to enable / disable it

It can be easily enabled, or disabled **through the DI**, which provides either the [`Profiler`](./src/Profiler.php) or the [`NullProfiler`](./src/NullProfiler.php).

```php
namespace PetrKnap\Profiler;

echo doSomething(new Profiler());
echo doSomething(new NullProfiler());
```

## Useful features

### Take snapshot

If you need to **measure the current values**, just call the `takeSnapshot` method on the [`Profiling`](./src/Profiling.php), or a [profiler](./src/ProfilerInterface.php).

```php
namespace PetrKnap\Profiler;

$profiling = Profiling::start();
// do something
$profiling->takeSnapshot();
// do something more
$profile = $profiling->finish();

printf('There are %d memory usage records.', count($profile->getMemoryUsages()));
```

If you want to automate it then [take snapshot on tick](#take-snapshot-on-tick).
Or you can use a more practical [cascade profiling](#cascade-profiling).

#### Take snapshot on tick

For greater precision, you can take **snapshot on each `N` tick**.

```php
declare(ticks=2); // this declaration is important (N=2)

namespace PetrKnap\Profiler;

$profiling = Profiling::start(takeSnapshotOnTick: true);
(fn () => 'something')();
$profile = $profiling->finish();

printf('There are %d memory usage records.', count($profile->getMemoryUsages()));
```

This will result in **very detailed code tracking**, which can degrade the performance of the monitored application.

### Cascade profiling

The `profile` method provides you a nested [profiler](./src/ProfilerInterface.php) that you can use for more detailed cascade profiling.

```php
namespace PetrKnap\Profiler;

$profile = (new Profiler())->profile(function (ProfilerInterface $profiler): void {
    // do something
    $profiler->profile(function (): void {
        // do something more
    });
});

printf('There are %d memory usage records.', count($profile->getMemoryUsages()));
```

---

Run `composer require petrknap/profiler` to install it.
You can [support this project via donation](https://petrknap.github.io/donate.html).
The project is licensed under [the terms of the `LGPL-3.0-or-later`](./COPYING.LESSER).
