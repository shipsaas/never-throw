# ShipSaaS NeverThrow for PHP

[![codecov](https://codecov.io/gh/shipsaas/never-throw/branch/main/graph/badge.svg?token=P1E3WP84MG)](https://codecov.io/gh/shipsaas/never-throw)
[![Build & Test (PHP 8.1, 8.2)](https://github.com/shipsaas/never-throw/actions/workflows/build.yml/badge.svg)](https://github.com/shipsaas/never-throw/actions/workflows/build.yml)

Provide a strictly typed way to deal with Success & Error Outcomes for your PHP applications.

Inspired by [NeverThrow](https://github.com/supermacro/neverthrow) so here we are with a simple implementation of NeverThrow from PHP.

> Throwing and catching is very similar to using goto statements - in other words; it makes reasoning about your programs harder. Secondly, by using throw you make the assumption that the caller of your function is implementing catch. This is a known source of errors. Example: One dev throws and another dev uses the function without prior knowledge that the function will throw. Thus, and edge case has been left unhandled and now you have unhappy users, bosses, cats, etc.
>
> With all that said, there are definitely good use cases for throwing in your program. But much less than you might think.

TL;DR: no throw = happier life & application. Fewer errors on production & increase the DRY & reusable things in your PHP apps.

## Supports
PHP 8.2+ since v2.0.0

## Installation

```bash
composer require shipsaas/never-throw
```

## Top-Level API

`NeverThrow\\Result` class and ready to be extended anytime.

### Non-static
- `isOk()`
- `isError()`
- `getOkResult()`
- `getErrorResult()`

## Usage

### Create your response classes

We'll create two new classes: Success & Error. And feel free to put any of your data 

```php
use NeverThrow\SuccessResult;
use NeverThrow\ErrorResult;

// Success
class BookShipperOkResult extends SuccessResult
{
    public function __construct(
        public string $bookingId
    ) {}
}

enum BookingErrors {
    case NO_SHIPPER_AVAILABLE;
    case OVERWEIGHT_PACKAGE;
    case INVALID_ADDRESS;
}

// Error
class BookShipperErrorResult extends ErrorResult
{
    public function __construct(
        public BookingErrors $outcome
    ) {}
}
```

### Create your dedicated Result class

Last step before using NeverThrow. Creating a dedicated Result class helps us to:

- Define types for the error & success result class
  - => IDE-friendly
  - => Happier reader/caller
- A centralized place that reference to the Success & Error result classes.

#### First way
```php
use NeverThrow\Result;

class BookShipperResult extends Result
{
    public function getOkResult(): BookShipperOkResult
    {
        return parent::getOkResult();
    }
    
    public function getErrorResult(): BookShipperErrorResult
    {
        return parent::getErrorResult();
    }
}
```

### Returns the Result in your business logic

```php
public function createBooking(User $user, BookingOrder $order): BookShipperResult
{
    $hasAnyShipperAvailable = $this->shipperService->hasAnyShipperAvailable();
    if (!$hasAnyShipperAvailable) {
        return new BookShipperResult(
            new BookShipperErrorResult(
                BookingErrors::NO_SHIPPER_AVAILABLE
            )
        );
    }
    
    $isOverweight = !$this->weightService->isValid($order);
    if ($isOverweight) {
        return new BookShipperResult(
            new BookShipperErrorResult(
                BookingErrors::OVERWEIGHT_PACKAGE
            )
        );
    }
    
    $bookingId = $this->book($user, $order);
   
    return new BookShipperResult(new BookShipperOkResult($bookingId));
}
```

### Check the Response

```php
$bookingResult = $this->service->createBooking($user, $order);

if ($bookingResult->isError()) {
    $errorResult = $bookingResult->getErrorResult();

    // handle error
    return showError(match ($errorResult->outcome) {
        BookingErrors::NO_SHIPPER_AVAILABLE => 'No shipper available at the moment. Please wait',
        BookingErrors::OVERWEIGHT_PACKAGE => 'Your package is overweight',
    });
}

return showBooking($bookingResult->getOkResult()->bookingId);
```

## Conclusion

As you can see with the above code, there are:

- no try/catch, 0 try/catch abuse
- no exception, never
- explicit return types & information.

It would bring the development truly awesome and no pain. 

With strictly typed return types, developers can know what is going on with other services/libraries. 
Thus makes the reusable better.
And we don't have to wrap the try/catch and uglify our code.

Don't abuse Exceptions, they should be only used for the unexpected situations (and Errors !== Exceptions, fact).

### Additional

```php
function transfer(): Transaction
{
    if (!$hasEnoughBalance) {
        thrown new InsufficientBalanceError();
    }
    
    if (!$invalidRecipient) {
        throw new InvalidRecipientError();
    }
    
    if (!$invalidMoney) {
        throw new InvalidTransferMoneyError();
    }
    
    $transaction = $this->transferService->transfer(...);
    if (!$transaction) {
        throw new TransferFailedError();
    }
    
    return $transaction;
}
```

Most of this function is actually about the things that can go wrong, but our types only inform us of the successful path. That means 4/5ths of the function's output is **untyped**!

The above "exceptions" or "errors" aren't really exceptions or errors at all. They are outcomes. They are predictable, reasonable parts of our system. My heuristic is, if they are something a good product manager would care about, they are not exceptions and you shouldn't throw them!

Exceptions are unpredictable things we cannot reasonably plan for, that the system should not attempt recovery from, and we should not route to the user.

## License
MIT License
