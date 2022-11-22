Lendable Clock Abstraction
====

[![Latest Stable Version](https://poser.pugx.org/lendable/clock/v/stable)](https://packagist.org/packages/lendable/clock)
[![License](https://poser.pugx.org/lendable/clock/license)](https://packagist.org/packages/lendable/clock)

Provides an object-oriented interface for retrieving the current time.

PHP of course provides `\DateTime`, `\DateTimeImmutable` and `\DateTimeInterface`. Why do we need a clock then? Why not just instantiate where required? 

We can obtain the current time with `$now = new \DateTime()` after all, why do we need a `Clock::now(): \DateTimeImmutable` style API to obtain the current time?

Depending on a Clock rather than constructing native PHP time objects ad hoc means you can both reason and control time. 

* Underlying implementation can be swapped out to one more suitable for a test environment.
  * Time can now be stubbed with a fixed value, or even start from a point in time and tick from there.
  * Interactions with the Clock can be observed and asserted on.
  * Time passing can be simulated with changes to the output from the Clock.
  * Tests that make poor assumptions can be avoided, i.e., capturing the current time to the second, then asserting that an expected output matches it. However, time passed during the setup to the assertion, and this time it crossed the second boundary.
* Clear dependencies on classes that require to obtain the current system time.
* The [PSR proposal for a Clock](https://github.com/php-fig/fig-standards/blob/master/proposed/clock-meta.md) contains further information and examples of current libraries and their solutions and workarounds to provide mocking support.

This library makes **no attempt** to mock global state, such as `\time()` or calls to `new \DateTimeImmutable()`. You will have conflicts if you cannot assert enough control over your dependencies to ensure all current system time retrieval goes through the Clock.

## Installation
You can install the library via [Composer](https://getcomposer.org/).

```bash
composer require lendable/clock
```

## Clock types
### `SystemClock`
Delegates to PHP for the current system time, uses a fixed timezone at construction.

Target: runtime

### `FixedClock`
Always provides a specific timestamp that is provided at construction.

Target: unit/functional tests 

### `TickingMockClock`
Mocks time starting from a given timestamp and simulates time progressing from that point. I.e a call to 
`TickingMockClock::now()` 200ms after it is created will give a time value 200ms after the given timestamp.

Target: unit/functional tests

### `PersistedFixedClock`
Similar to `FixedClock`, but can persist and load the given timestamp from disk.
Use `PersistedFixedClock::initializeWith(...)` to set up the timestamp and `PersistedFixedClock::fromPersisted(...)`
to load from the persisted value on disk.

Target: functional tests where you reload your context. E.g. Behat vs Symfony Kernel. You
would initialize in a `BeforeScenario` hook and then load the data from within the Kernel. 
