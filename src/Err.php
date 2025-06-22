<?php

namespace NeverThrow;

use LogicException;

/**
 * @template T
 *
 * @implements ResultInterface<never, T>
 */
class Err implements ResultInterface
{
    /**
     * @param T $errValue
     */
    public function __construct(public mixed $errValue)
    {
    }

    public function isOk(): bool
    {
        return false;
    }

    public function isErr(): bool
    {
        return true;
    }

    public function unwrap(): mixed
    {
        throw new LogicException('Cannot unwrap an error from Ok result');
    }

    /**
     * @return T
     */
    public function unwrapErr(): mixed
    {
        return $this->errValue;
    }
}
