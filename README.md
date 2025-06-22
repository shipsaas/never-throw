# ShipSaaS - NeverThrow for PHP

[![codecov](https://codecov.io/gh/shipsaas/never-throw/branch/main/graph/badge.svg?token=P1E3WP84MG)](https://codecov.io/gh/shipsaas/never-throw)
[![Build & Test (PHP 8.1, 8.2)](https://github.com/shipsaas/never-throw/actions/workflows/build.yml/badge.svg)](https://github.com/shipsaas/never-throw/actions/workflows/build.yml)

Provide a strictly typed way to deal with Success & Error Outcomes for your PHP applications.

Inspired by [NeverThrow](https://github.com/supermacro/neverthrow) so here we are with a simple implementation of NeverThrow from PHP.

> Throwing and catching is very similar to using goto statements - in other words; it makes reasoning about your programs harder. Secondly, by using throw you make the assumption that the caller of your function is implementing catch. This is a known source of errors. Example: One dev throws and another dev uses the function without prior knowledge that the function will throw. Thus, and edge case has been left unhandled and now you have unhappy users, bosses, cats, etc.
>
> With all that said, there are definitely good use cases for throwing in your program. But much less than you might think.

TL;DR: no throw = happier life & application. Fewer errors on production & increase the DRY & reusable things in your PHP apps.

## Supports
PHP 8.2+

## Installation

```bash
composer require shipsaas/never-throw
```

## Top-Level API

We provide 2 main classes:
- `Ok`
- `Err`

You can use them immediately in your code, but we recommend to create your own Success & Error classes to make it more readable and IDE-friendly.

## Usage

### Create your success & error data classes

```php
class BookShipperOkData
{
    public function __construct(
        public Booking $booking
    ) {}
}

enum BookingErrors {
    case NO_SHIPPER_AVAILABLE;
    case OVERWEIGHT_PACKAGE;
    case INVALID_ADDRESS;
}

// Error
class BookShipperErrorData
{
    public function __construct(
        public BookingErrors $code
    ) {}
}
```

### Returns the Result in your business logic


```php
use NeverThrow\Ok;
use NeverThrow\Err;
use NeverThrow\ResultInterface;

/**
 * @return ResultInterface<BookShipperOkData, BookShipperErrorData>
 */
public function createBooking(User $user, BookingOrder $order): ResultInterface
{
    $hasAnyShipperAvailable = $this->shipperService->hasAnyShipperAvailable();
    if (!$hasAnyShipperAvailable) {
        return new Err(BookingErrors::NO_SHIPPER_AVAILABLE);
    }
    
    $isOverweight = !$this->weightService->isValid($order);
    if ($isOverweight) {
        return new Err(BookingErrors::OVERWEIGHT_PACKAGE);
    }
    
    $booking = $this->book($user, $order);
   
    return new Ok($booking);
}
```

### Check the Response

```php
$bookingResult = $this->service->createBooking($user, $order);

if ($bookingResult->isError()) {
    $errorCode = $bookingResult->unwrapErr()->code;

    // handle error
    return showError(match ($errorCode) {
        BookingErrors::NO_SHIPPER_AVAILABLE => 'No shipper available at the moment. Please wait',
        BookingErrors::OVERWEIGHT_PACKAGE => 'Your package is overweight',
    });
}

return showBooking([
    'booking' => $bookingResult->unwrapOk()->booking,
]);
```

## Conclusion

As you can see with the above code, there are:

- no try/catch, 0 try/catch abuse
- no exception, never
- explicit return types & information.

It would bring the development truly awesome and no pain. 

With strictly typed return types, developers can know what is going on when using services/libraries (thus makes the reusable better).

And it makes us worry-free about wrapping things in try/catch blocks.

Let us don't abuse/overuse Exceptions, they should be only used for unexpected situations.

(Additional thoughts: Exceptions are expensive, way more expensive than a simple Error response)

### Additional notes

```php
function transfer(): Transaction
{
    if (!$this->hasEnoughBalance()) {
        throw new InsufficientBalanceError();
    }
    
    if (!$this->isValidRecipient()) {
        throw new InvalidRecipientError();
    }
    
    if (!$this->isValidTransferAmount()) {
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

The above "exceptions" or "errors" aren't really exceptions or errors at all. They are outcomes. They are predictable, reasonable parts of our system. My heuristic is, if they are something a good product manager would care about, they are not exceptions, and you shouldn't throw them!

Exceptions are unpredictable things we cannot reasonably plan for, that the system should not attempt recovery from, and we should not route to the user.

## License
MIT License
