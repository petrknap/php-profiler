# php-profiler

PHP profiler by [Petr Knap].

* [What is profiling?](#what-is-profiling)
* [Components](#components)
    * [Profile](#profile)
    * [SimpleProfiler](#simpleprofiler)
        * [Usage](#usage)
    * [AdvancedProfiler](#advancedprofiler)
* [How to install](#how-to-install)
    * [Versioning](#versioning)


## What is profiling?

> In software engineering, **profiling** (*"program profiling"*, *"software profiling"*) is a form of dynamic program analysis that measures, for example, the space (memory) or time complexity of a program, the usage of particular instructions, or the frequency and duration of function calls. Most commonly, profiling information serves **to aid program optimization**.
-- [Profiling (computer programming) - Wikipedia, The Free Encyclopedia]


## Components

### Profile

[`Profile`] is base data structure returned by profilers.


### SimpleProfiler

[`SimpleProfiler`] is easy-to-use and quick static class for PHP code profiling. You can extend it and make your own specific profiler just for your use-case.

#### Usage

```php
SimpleProfiler::enable();            // Enable profiler
echo "Hello";                        // Do what you need to do before you start profiling
SimpleProfiler::start();             // Start profiling where you wish to start profiling
echo " ";                            // Do what you need to profile here
$profile = SimpleProfiler::finish(); // Finish profiling where you wish to finish profiling
echo "World";                        // Do what you need to do after you finish profiling
var_dump($profile);                  // Process your profile here
```


### AdvancedProfiler

[`AdvancedProfiler`] is advanced version of [`SimpleProfiler`] and is developed dynamically. If you want to see an example of usage, then visit [`AdvancedProfilerTest`].


## How to install

Run `composer require petrknap/php-profiler` or merge this JSON code with your project `composer.json` file manually and run `composer install`. Instead of `dev-master` you can use [one of released versions].

```json
{
    "require": {
        "petrknap/php-profiler": "dev-master"
    }
}
```

Or manually clone this repository via `git clone https://github.com/petrknap/php-profiler.git` or download [this repository as ZIP] and extract files into your project.

### Versioning

Version is sequence of 4 numbers separated by dot (for example `1.2.3.4`). First pair of numbers is [`Profile`] version, second pair is reflection of changes in [`SimpleProfiler`] and [`AdvancedProfiler`] versions.

```
       Profile     SimpleProfiler
       |     |     |     |
     (maj) (min) (c+1) (d+1)
       |     |     |     |
       a  .  b  .  c  .  d
                   |     |
                 (c+1) (d+1)
                   |     |
                   AdvancedProfiler
```

If you wish to fix:
* [`Profile`] major version use `1.*`
* [`Profile`] minor version use `1.2.*`
* [`SimpleProfiler`] or [`AdvancedProfiler`] major version use `1.2.3.*`
* exact version use `1.2.3.4`



[Petr Knap]:http://petrknap.cz/
[Profiling (computer programming) - Wikipedia, The Free Encyclopedia]:https://en.wikipedia.org/w/index.php?title=Profiling_(computer_programming)&oldid=697419059
[`Profile`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/Profile.php
[`SimpleProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/SimpleProfiler.php
[`AdvancedProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/AdvancedProfiler.php
[`AdvancedProfilerTest`]:https://github.com/petrknap/php-profiler/blob/master/tests/Profiler/AdvancedProfilerTest.php
[one of released versions]:https://github.com/petrknap/php-profiler/releases
[this repository as ZIP]:https://github.com/petrknap/php-profiler/archive/master.zip
