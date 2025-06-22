<?php

namespace NeverThrow\Tests;

use LogicException;
use NeverThrow\Err;
use PHPUnit\Framework\TestCase;

class ErrTest extends TestCase
{
    public function testIsOkReturnsFalse(): void
    {
        $this->assertFalse((new Err(1))->isOk());
    }

    public function testIsErrReturnsTrue(): void
    {
        $this->assertTrue((new Err(1))->isErr());
    }

    public function testUnwrapErrReturnsErrData(): void
    {
        $this->assertSame(1, (new Err(1))->unwrapErr());
    }

    public function testGenericType(): void
    {
        $err = new Err(new ErrorData('error_failed'));

        $this->assertSame('error_failed', $err->unwrapErr()->code);
    }

    public function testUnwrapThrowsException(): void
    {
        $this->expectException(LogicException::class);

        (new Err(1))->unwrap();
    }
}

class ErrorData {
    public function __construct(public string $code) {}
}
