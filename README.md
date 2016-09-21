# php-profiler

PHP profiler by [Petr Knap].

* [What is profiling?](#what-is-profiling)
* [Components](#components)
    * [Profile](#profile)
    * [SimpleProfiler](#simpleprofiler)
        * [Usage](#usage)
    * [AdvancedProfiler](#advancedprofiler)
* [How to install](#how-to-install)


## What is profiling?

> In software engineering, **profiling** (*"program profiling"*, *"software profiling"*) is a form of dynamic program analysis that measures, for example, the space (memory) or time complexity of a program, the usage of particular instructions, or the frequency and duration of function calls. Most commonly, profiling information serves **to aid program optimization**.
-- [Profiling (computer programming) - Wikipedia, The Free Encyclopedia]


## Components

### Profile

[`Profile`] is base data structure returned by profilers.


### SimpleProfiler

[`SimpleProfiler`] is easy-to-use and quick static class for PHP code profiling. You can extend it and make your own specific profiler just for your use-case.

#### Usage

If you wish to profile a block of code, simply encapsulate it between `SimpleProfiler::start` and `SimpleProfiler::finish` calls.

```php
SimpleProfiler::enable();
SimpleProfiler::start();
/* your code goes here */
processProfile(SimpleProfiler::finish());
```

If you wish, you can add labels to your profiles. The syntax is same as for `sprintf`.

```php
SimpleProfiler::start(/* sprintf( */ "static label" /* ) */);
SimpleProfiler::start(/* sprintf( */ "line %s", __LINE__ /* ) */);
```

If you wish to create more detailed profiles, start new profile inside another one.

```php
SimpleProfiler::start("Profile 1");
    /* your code goes here */
    SimpleProfiler::start("Profile 1.1");
        SimpleProfiler::start("Profile 1.1.1");
            /* your code goes here */
        SimpleProfiler::finish("Profile 1.1.1");
        /* your code goes here */
        SimpleProfiler::start("Profile 1.1.2");
            /* your code goes here */
        SimpleProfiler::finish("Profile 1.1.2");
        /* your code goes here */
    SimpleProfiler::finish("Profile 1.1");
SimpleProfiler::finish("Profile 1");
```


### AdvancedProfiler

[`AdvancedProfiler`] is advanced version of [`SimpleProfiler`]. If you want to see an example of usage, then visit [`SimpleProfiler` section](#simpleprofiler).


## How to install

Run `composer require petrknap/php-profiler` in your project directory.



[Petr Knap]:http://petrknap.cz/
[Profiling (computer programming) - Wikipedia, The Free Encyclopedia]:https://en.wikipedia.org/w/index.php?title=Profiling_(computer_programming)&oldid=697419059
[`Profile`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/Profile.php
[`SimpleProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/SimpleProfiler.php
[`AdvancedProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/AdvancedProfiler.php
