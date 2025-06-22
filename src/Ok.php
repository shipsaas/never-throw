<?php

namespace NeverThrow;

use LogicException;

/**
 * @template T
 *
 * @implements ResultInterface<T, never>
 */
class Ok implements ResultInterface
{
    /**
     * @param T $okValue
     */
    public function __construct(public mixed $okValue)
    {
    }

    public function isOk(): bool
    {
        return true;
    }

    public function isErr(): bool
    {
        return false;
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        return $this->okValue;
    }

    public function unwrapErr()
    {
        throw new LogicException('Cannot unwrap an error from Ok result');
    }
}
