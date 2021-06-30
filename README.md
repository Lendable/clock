Lendable Clock Abstraction
====

[![Latest Stable Version](https://poser.pugx.org/lendable/clock/v/stable)](https://packagist.org/packages/lendable/clock)
[![License](https://poser.pugx.org/lendable/clock/license)](https://packagist.org/packages/lendable/clock)

Provides an object oriented interface for retrieving the current time.

This serves to increase the reliability of time sensitive tests, preventing data fixtures
from having to be created with relative-to-now timestamps and having potential failures where
the clock ticks over unexpectedly.

## Installation
```bash
composer require lendable/clock
```

## Clock types
### `SystemClock`
Delegates to PHP for the current system time, uses a fixed timezone at construction.

Target: runtime

### `FixedClock`
Always provides a specific timestamp that is provided at construction.

Target: unit tests 

### `PersistedFixedClock`
Similar to `FixedClock`, but can persist and load the given timestamp from disk.
Use `PersistedFixedClock::initializeWith(...)` to set up the timestamp and `PersistedFixedClock::fromPersisted(...)`
to load from the persisted value on disk.

Target: functional tests where you reload your context. E.g. Behat vs Symfony Kernel. You
would initialize in a `BeforeScenario` hook and then load the data from within the Kernel. 

 
