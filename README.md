# Lendable Clock

[![Latest Stable Version](https://poser.pugx.org/lendable/clock/v/stable)](https://packagist.org/packages/lendable/clock)
[![License](https://poser.pugx.org/lendable/clock/license)](https://packagist.org/packages/lendable/clock)

The Lendable Clock library provides an object-oriented interface for accessing the system time in PHP. While PHP offers direct instantiation of `\DateTime`, and `\DateTimeImmutable` to obtain the current system time, this library introduces the concept of a Clock to offer greater control and flexibility over time-related operations.

## Why Use a Clock?

You might wonder why you need a clock when you can simply instantiate `\DateTime` objects whenever you need them. Here's why a Clock abstraction is beneficial:

- **Control Over Time**: By depending on a Clock rather than instantiating time objects directly, you gain the ability to reason about and control time within your application.

- **Testing Flexibility**: Using a Clock allows you to swap underlying implementations, making it easier to test time-dependent code. You can stub time with fixed values, simulate time passing, and observe interactions with the Clock for more robust testing.

- **Dependency Management**: Clear dependencies on the Clock class help in managing components that rely on accessing the current system time.

- **PSR-20 Compatibility**: The library aligns with [PSR-20](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-20-clock-meta.md), offering interoperability with other libraries and frameworks.

## Installation

You can install the Lendable Clock library via [Composer](https://getcomposer.org/).

```bash
composer require lendable/clock
```

## Clock Types

The library provides several types of Clocks to suit different use cases:

### `SystemClock`

- **Target**: Runtime
- **Description**: Delegates to PHP for the current system time, using a fixed timezone at construction.

### `FixedClock`

- **Target**: Unit/Functional Tests
- **Description**: Always provides a specific timestamp provided at construction, facilitating deterministic testing.

```php
$clock = new FixedClock(new \DateTimeImmutable('2024-03-01 14:19:41'));

echo $clock->now()->format('Y-m-d H:i:s'), "\n";
sleep(5);
echo $clock->now()->format('Y-m-d H:i:s'), "\n";
```

```
2024-03-01 14:19:41
2024-03-01 14:19:41
```

### `TickingMockClock`

- **Target**: Unit/Functional Tests
- **Description**: Mocks time starting from a given timestamp and simulates time progressing from that point. Useful for testing time-dependent functionality.

```php
$clock = TickingMockClock::tickingFromCurrentTime(new \DateTimeImmutable('2024-03-01 14:19:41'));

echo $clock->now()->format('Y-m-d H:i:s.u'), "\n";
sleep(5);
echo $clock->now()->format('Y-m-d H:i:s.u'), "\n";

```

```
2024-03-01 14:19:41.000006
2024-03-01 14:19:46.005175
```

### `PersistedFixedClock`

- **Target**: Functional Tests (e.g., Behat vs. Symfony Kernel)
- **Description**: Similar to `FixedClock`, but can persist and load the given timestamp from disk. Ideal for scenarios where you need to reload your context during testing.

Use `PersistedFixedClock::initializeWith(...)` to set up the timestamp and `PersistedFixedClock::fromPersisted(...)` to load from the persisted value on disk.

By leveraging these Clock types, you can enhance the reliability, testability, and maintainability of your time-dependent PHP applications.

```php
$clock = PersistedFixedClock::initializeWith(
    __DIR__,
    new FixedFileNameGenerator('time.json'),
    new \DateTimeImmutable('2024-03-01 14:19:41'),
);

echo $clock->now()->format('Y-m-d H:i:s.u'), "\n";

sleep(5);

$clock = PersistedFixedClock::fromPersisted(__DIR__, new FixedFileNameGenerator('time.json'));

echo $clock->now()->format('Y-m-d H:i:s.u'), "\n";
```

```
2024-03-01 14:19:41.000000
2024-03-01 14:19:41.000000
```
