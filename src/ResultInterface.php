<?php

namespace NeverThrow;

/**
 * @template T
 * @template-covariant E
 */
interface ResultInterface
{
    public function isOk(): bool;

    public function isErr(): bool;

    /**
     * @return T
     *
     * @throws \LogicException if the result is an error
     */
    public function unwrap();

    /**
     * @return E
     *
     * @throws \LogicException if the result is ok
     */
    public function unwrapErr();
}
