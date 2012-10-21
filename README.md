Clutch
======

Clutch can make fight several PHP code to know which is the best in under certain conditions.

This project is still a work in progress.

Installation
------------
Use [**Composer**](https://github.com/composer/composer) to install : `free-agent/clutch`.

Configuration
-------------
### Bootstrap
Create a `competitions` folder in your project. Inside it, create a `bootstrap.php` file and add a new namespace for the `Competitions`:

**competitions/bootstrap.php**:

```php
<?php

error_reporting(E_ALL);

$loader = require __DIR__.'/../src/bootstrap.php';
$loader->add('ChuckNorris\Competitions', __DIR__);
```

Continuing with the above example, now create the competition structure : `competitions/ChuckNorris/Competitions`.

### Competition
Now create a first competition. For example, we want to benchmark several array solutions. The competition is a PHP class, so create a new folder `ArrayComparison` with the `Competition.php` file inside:

**competitions/ChuckNorris/Competitions/ArrayComparison/Competition.php**:

```php
<?php

namespace ChuckNorris\Competitions\ArrayComparison;

use ChuckNorris\Competitions\ArrayComparison\Fighters\ArrayFighter;
use ChuckNorris\Competitions\ArrayComparison\Fighters\ArrayObjectFighter;
use ChuckNorris\Competitions\ArrayComparison\Rounds\Int100ElementsRound;
use ChuckNorris\Competitions\ArrayComparison\Rounds\String100ElementsRound;
use ChuckNorris\Competitions\ArrayComparison\Rounds\Object100ElementsRound;
use Clutch\Competition\AbstractCompetition;
use Clutch\Instrument\Chronometer;
use Clutch\Instrument\Memorymeter;

class Competition extends AbstractCompetition
{
    public function getName()
    {
        return 'ArrayComparison';
    }

    public function getDescription()
    {
        return 'ArrayComparison for finding the best PHP code';
    }

    public function getIteration()
    {
        return 10;
    }

    public function __construct()
    {
        $this
            ->addFighter(new ArrayFighter)
            ->addFighter(new ArrayObjectFighter)
            ->addInstrument(new Chronometer)
            ->addInstrument(new Memorymeter)
            ->addRound(new Int100ElementsRound)
            ->addRound(new String100ElementsRound)
            ->addRound(new Object100ElementsRound)
        ;
    }
}
```

### Round
Now we need a `Round`. The rounds are added to the `Competition` with the `addRound` method.

**competitions/ChuckNorris/Competitions/ArrayComparison/Rounds/Int100ElementsRound.php**:

```php
<?php

namespace ChuckNorris\Competitions\ArrayComparison\Rounds;

use Clutch\Competition\AbstractRound;

class Int100ElementsRound extends AbstractRound
{
    public function getName()
    {
        return 'Int100Elements';
    }
}
```

In the example of the `ArrayComparison` competition, we need to create two others rounds.

**competitions/ChuckNorris/Competitions/ArrayComparison/Rounds/String100ElementsRound.php**:

```php
<?php

namespace ChuckNorris\Competitions\ArrayComparison\Rounds;

use Clutch\Competition\AbstractRound;

class String100ElementsRound extends AbstractRound
{
    public function getName()
    {
        return 'String100Elements';
    }
}
```

**competitions/ChuckNorris/Competitions/ArrayComparison/Rounds/Object100ElementsRound.php**:

```php
<?php

namespace ChuckNorris\Competitions\ArrayComparison\Rounds;

use Clutch\Competition\AbstractRound;

class Object100ElementsRound extends AbstractRound
{
    public function getName()
    {
        return 'Object100Elements';
    }
}
```

### Fighter
And now… the first `Fighter` ! The fighters are added to the `Competition` with the `addFighter` method. For each round you need to create a method in the `Fighter`.

**competitions/ChuckNorris/Competitions/ArrayComparison/Fighters/ArrayFighter.php**:

```php
<?php

namespace ChuckNorris\Competitions\ArrayComparison\Fighters;

use Clutch\Competition\AbstractFighter;

class ArrayFighter extends AbstractFighter
{
    public function getName()
    {
        return 'Array';
    }

    public function getDescription()
    {
        return 'The native PHP array';
    }

    public function fightInt100ElementsRound()
    {
        $fighter = array();

        for ($i = 0; $i < 10; $i++) {
            $fighter[$i] = $i;
        }

        return $fighter;
    }

    public function fightObject100ElementsRound()
    {
        $fighter = array();

        for ($i = 0; $i < 10; $i++) {
            $fighter[$i] = new \DateTime();
        }

        return $fighter;
    }

    public function fightString100ElementsRound()
    {
        $fighter = array();

        for ($i = 0; $i < 10; $i++) {
            $fighter[$i] = 'Chuck Norris';
        }

        return $fighter;
    }
}
```

**competitions/ChuckNorris/Competitions/ArrayComparison/Fighters/ArrayObjectFighter.php**:

```php
<?php

namespace ChuckNorris\Competitions\ArrayComparison\Fighters;

use Clutch\Competition\AbstractFighter;

class ArrayObjectFighter extends AbstractFighter
{
    public function getName()
    {
        return 'ArrayObject';
    }

    public function getDescription()
    {
        return 'The native PHP array';
    }

    public function fightInt100ElementsRound()
    {
        $fighter = new \ArrayObject;

        for ($i = 0; $i < 10; $i++) {
            $fighter[$i] = $i;
        }

        return $fighter;
    }

    public function fightObject100ElementsRound()
    {
        $fighter = new \ArrayObject;

        for ($i = 0; $i < 10; $i++) {
            $fighter[$i] = new \DateTime();
        }

        return $fighter;
    }

    public function fightString100ElementsRound()
    {
        $fighter = new \ArrayObject;

        for ($i = 0; $i < 10; $i++) {
            $fighter[$i] = 'Chuck Norris';
        }

        return $fighter;
    }
}
```

### Instrument
An `Instrument` will tell you the winner. The instruments are added to the `Competition` with the `addInstrument` method.
There are 2 instruments :

- Chronometer
- Memorymeter

Usage
-----
We have now a competition with 3 rounds and 2 fighters.

```shell
$ php bin/clutch fight ChuckNorris/ArrayComparison
Welcome to the ArrayComparison competition !
Here are the competitors who will fight each others:
     - Array - The native PHP array
     - ArrayObject - The native PHP array

✭ Round 1: Int100Elements - Ready ? Fight !
     And the winner is...
     Chronometer (µs):
          1. Array with 2.8491020202637E-5 (±9.0599060058595E-6)
          2. ArrayObject with 4.0984153747559E-5 (±7.033348083496E-6)
     Memorymeter (bytes):
          1. Array with 544 (±0)
          2. ArrayObject with 576 (±0)

✭ Round 2: String100Elements - Ready ? Fight !
     And the winner is...
     Chronometer (µs):
          1. Array with 2.9850006103516E-5 (±1.3947486877441E-5)
          2. ArrayObject with 3.7384033203125E-5 (±4.5299530029295E-6)
     Memorymeter (bytes):
          1. Array with 544 (±0)
          2. ArrayObject with 576 (±0)

✭ Round 3: Object100Elements - Ready ? Fight !
     And the winner is...
     Chronometer (µs):
          1. Array with 0.00017499923706055 (±2.6464462280275E-5)
          2. ArrayObject with 0.00019152164459229 (±6.389617919922E-5)
     Memorymeter (bytes):
          1. Array with 848 (±0)
          2. ArrayObject with 864 (±0)

Thank you for watching us !
```
