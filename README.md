# php-profiler

PHP profiler by [Petr Knap].

* [Components](#components)
    * [Profile](#profile)
    * [SimpleProfiler](#simpleprofiler)
        * [Usage of SimpleProfiler](#usage-of-simpleprofiler)
    * [AdvancedProfiler](#advancedprofiler)
        * [Usage of AdvancedProfiler](#usage-of-advancedprofiler)
* [How to install](#how-to-install)
    * [Versioning](#versioning)


## Components

### Profile

[`Profile`] is base data structure returned by profilers.


### SimpleProfiler

[`SimpleProfiler`] is easy-to-use and quick static class for PHP code profiling. You can extend it and make your own specific profiler just for your use-case.

#### Usage of SimpleProfiler

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

[`AdvancedProfiler`] is advanced version of [`SimpleProfiler`].

#### Usage of AdvancedProfiler

[`AdvancedProfiler`] is dynamically developed. If you want to see an example of usage visit [`AdvancedProfilerTest`].


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
[`Profile`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/Profile.php
[`SimpleProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/SimpleProfiler.php
[`AdvancedProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/AdvancedProfiler.php
[`AdvancedProfilerTest`]:https://github.com/petrknap/php-profiler/blob/master/tests/Profiler/AdvancedProfilerTest.php
[one of released versions]:https://github.com/petrknap/php-profiler/releases
[this repository as ZIP]:https://github.com/petrknap/php-profiler/archive/master.zip
