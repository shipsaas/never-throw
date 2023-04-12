# NeverThrow - PHP Version

[![Build & Test (PHP 8.1, 8.2)](https://github.com/shipsaas/never-throw/actions/workflows/build.yml/badge.svg)](https://github.com/shipsaas/never-throw/actions/workflows/build.yml)

Inspired by [NeverThrow](https://github.com/supermacro/neverthrow) so here we are with a simple implementation of NeverThrow from PHP.

See more [notes of NeverThrow](https://github.com/supermacro/neverthrow#a-note-on-the-package-name) on the whys.

No throw = happier life & application. 

Use the simple `ok` and `error` result for your application code with a strictly-typed way in PHP.

## Supports
PHP 8.1+

## Installation

```bash
composer require shipsaas/never-throw
```

## Top-Level API

`NeverThrow\\Result` class and ready to be extended anytime.

### Static
- `Result::ok(..)`: create an `ok` result.
- `Result::error(..)`: create an `error` result.

### Non-static
- `isOk()`
- `isError()`
- `getOkResult()`
- `getErrorResult()`

## Usage

### Create your response classes

Basically, A simple class to hold `ok` or `error` data.

For example, I'll create 2 new classes - 1 ok and 1 error.

```php
class BookShipperOkResult 
{
    public function __construct(
        public string $bookingId
    ) {}
}

class BookShipperErrorResult
{
    // note even more strict, you can use `enum` - php8.1
    public function __construct(
        public string $outcome
    ) {}
}
```

### Create your new Result class

The reason why you need to create your own Result class:

- Cast the correct types for the error & success.

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

#### Second way

```php
use NeverThrow\Result;

/**
 * @method BookShipperOkResult getOkResult() 
 * @method BookShipperErrorResult getErrorResult()
 */
class BookShipperResult extends Result
{
}
```

### Set Error / OK 

```php
public function createBooking(User $user, BookingOrder $order): BookShipperResult
{
    if (!$user->hasEnoughtBalance()) {
        return BookShipperResult::error(new BookShipperErrorResult('INSUFFICIENT_BALANCE'));
    }
    
    if (!$order->isValid()) {
        return BookShipperResult::error(new BookShipperErrorResult('ORDER_IS_INVALID'));
    }
    
    $bookingId = $this->book($user, $order);
   
    return BookShipperResult::ok(new BookShipperOkResult($bookingId));
}
```

### Check the Response

```php
$bookingResult = $this->service->createBooking($user, $order);

if ($bookingResult->isError()) {
    // handle error
    return showError(match ($bookingResult->getErrorResult()->outcome) {
        'INSUFFICIENT_BALANCE' => 'Not enough balance to make the booking',
        'ORDER_IS_INVALID' => 'The order is invalid (probably expired)',
    });
}

return showBooking($bookingResult->getOkResult()->bookingId);
```

## Conclusion

As you can see, there is no:

- try/catch everywhere, try/catch abuse
- no exception, never

Only ok/err response. It would bring the development truly awesome and no pain. There won't be any error thrown in a middle of a big flow.

With strictly-typed return types, developers can know what is going on with other's services/libraries, it makes the
reusable skyrocketing. And they don't have to wrap the try/catch while using the stuff.

Don't abuse Exceptions, they should be used for the unexpected things. And error !== exception.

## Contributor
Seth Phat

## License
MIT License
