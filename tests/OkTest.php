<?php

namespace NeverThrow\Tests;

use LogicException;
use NeverThrow\Ok;
use PHPUnit\Framework\TestCase;

class OkTest extends TestCase
{
    public function testIsOkReturnsTrue(): void
    {
        $this->assertTrue((new Ok(1))->isOk());
    }

    public function testIsErrReturnsFalse(): void
    {
        $this->assertFalse((new Ok(1))->isErr());
    }

    public function testUnwrapReturnsSuccessData(): void
    {
        $this->assertSame(1, (new Ok(1))->unwrap());
    }

    public function testGenericType(): void
    {
        $ok = new Ok(new SuccessData(10));

        $this->assertSame(10, $ok->unwrap()->id);
    }

    public function testUnwrapErrThrowsException(): void
    {
        $this->expectException(LogicException::class);

        (new Ok(1))->unwrapErr();
    }
}

class SuccessData {
    public function __construct(public int $id) {}
}
