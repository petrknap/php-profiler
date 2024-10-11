# PHP profiler for short-term & long-term profiling

This tool allows you to monitor performance and detect memory leaks as well as inconsistent performance behavior of your application over time.

## Short-term profiling

For **short-term profiling** you can use a [profiling helper](./src/Profiling.php).
The [`Profiling`](./src/Profiling.php) will allow you to profile between `start` and `finish` methods calls.

```php
namespace PetrKnap\Profiler;

$profiling = Profiling::start();
// do something
$profile = $profiling->finish();

printf('It took %.1f s to do something.', $profile->getDuration());
```

The [`Profiling`](./src/Profiling.php) is simple - **cannot be turned on and off** easily.
So a [profiler](./src/ProfilerInterface.php) was created for the purpose of hard-coded long-term profiling.

## Long-term profiling

Request a [profiler](./src/ProfilerInterface.php) as a dependency and call a `profile` method on it.

```php
namespace PetrKnap\Profiler;

function something(ProfilerInterface $profiler): string {
    // do something without profiling
    return $profiler->profile(function (): string {
        // do something
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

echo something(new Profiler());
echo something(new NullProfiler());
```

### Cascade profiling

The `profile` method provides you a nested [profiler](./src/ProfilerInterface.php) that you can use for more detailed cascade profiling.

```php
namespace PetrKnap\Profiler;

echo (new Profiler())->profile(function (ProfilerInterface $profiler): string {
    // do something before something
    return something($profiler);
})->process(fn (ProfileInterface $profile) => printf(
    'It took %.1f s to do something before something and something, there are %d children profiles.',
    $profile->getDuration(),
    count($profile->getChildren()),
));
```

---

Run `composer require petrknap/profiler` to install it.
You can [support this project via donation](https://petrknap.github.io/donate.html).
The project is licensed under [the terms of the `LGPL-3.0-or-later`](./COPYING.LESSER).
