# PHP profiler

* [What is profiling?](#what-is-profiling)
* [Components](#components)
    * [Profile](#profile)
        * [Usage](#usage)
    * [SimpleProfiler](#simpleprofiler)
    * [AdvancedProfiler](#advancedprofiler)
* [How to install](#how-to-install)


## What is profiling?

> In software engineering, **profiling** (*"program profiling"*, *"software profiling"*) is a form of dynamic program analysis that measures, for example, the space (memory) or time complexity of a program, the usage of particular instructions, or the frequency and duration of function calls. Most commonly, profiling information serves **to aid program optimization**.
-- [Profiling (computer programming) - Wikipedia, The Free Encyclopedia]


## Components

### Profile

[`Profile`] is base data structure returned by profilers and wrapper for chosen one.

#### Usage

If you wish to profile a block of code, simply encapsulate it between `Profile::start` and `Profile::finish` calls.

```php
<?php

use PetrKnap\Php\Profiler\Profile;
use PetrKnap\Php\Profiler\SimpleProfiler;

SimpleProfiler::enable();
Profile::setProfiler(SimpleProfiler::class);

Profile::start();
/* your code goes here */
var_dump(Profile::finish());
```

If you wish, you can add labels to your profiles. The syntax is same as for `sprintf`.

```php
<?php

use PetrKnap\Php\Profiler\Profile;

Profile::start(/* sprintf( */ "static label" /* ) */);
Profile::start(/* sprintf( */ "line %s", __LINE__ /* ) */);
```

If you wish to create more detailed profiles, start new profile inside another one.

```php
<?php

use PetrKnap\Php\Profiler\Profile;

Profile::start("Profile 1");
    /* your code goes here */
    Profile::start("Profile 1.1");
        Profile::start("Profile 1.1.1");
            /* your code goes here */
        Profile::finish("Profile 1.1.1");
        /* your code goes here */
        Profile::start("Profile 1.1.2");
            /* your code goes here */
        Profile::finish("Profile 1.1.2");
        /* your code goes here */
    Profile::finish("Profile 1.1");
Profile::finish("Profile 1");
```

Or (if you wish) you can call `start` and `finish` methods directly on requested profiler.


### SimpleProfiler

[`SimpleProfiler`] is easy-to-use and quick static class for PHP code profiling. You can extend it and make your own specific profiler just for your use-case.

```php
<?php

use PetrKnap\Php\Profiler\SimpleProfiler;

SimpleProfiler::enable();

SimpleProfiler::start();
/* your code goes here */
var_dump(SimpleProfiler::finish());
```


### AdvancedProfiler

[`AdvancedProfiler`] is advanced version of [`SimpleProfiler`] with support for post processor.

```php
<?php

use PetrKnap\Php\Profiler\AdvancedProfiler;
use PetrKnap\Php\Profiler\Profile;

AdvancedProfiler::setPostProcessor(function(Profile $profile) {
    var_dump($profile);
});
AdvancedProfiler::enable();

AdvancedProfiler::start();
/* your code goes here */
AdvancedProfiler::finish();
```


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



[one of released versions]:https://github.com/petrknap/php-profiler/releases
[this repository as ZIP]:https://github.com/petrknap/php-profiler/archive/master.zip




[Profiling (computer programming) - Wikipedia, The Free Encyclopedia]:https://en.wikipedia.org/w/index.php?title=Profiling_(computer_programming)&oldid=697419059
[`Profile`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/Profile.php
[`SimpleProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/SimpleProfiler.php
[`AdvancedProfiler`]:https://github.com/petrknap/php-profiler/blob/master/src/Profiler/AdvancedProfiler.php
