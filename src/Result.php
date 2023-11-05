<?php

namespace NeverThrow;

use LogicException;

abstract class Result
{
    private bool $isOk;
    private SuccessResult|ErrorResult $result;

    public function __construct(SuccessResult|ErrorResult $result)
    {
        $this->isOk = $result->isOk();
        $this->result = $result;
    }

    public function isOk(): bool
    {
        return $this->isOk;
    }

    public function isError(): bool
    {
        return !$this->isOk;
    }

    /**
     * @throws LogicException
     */
    public function getOkResult(): SuccessResult
    {
        if ($this->isError()) {
            throw new LogicException('Result is not OK');
        }

        return $this->result;
    }

    /**
     * @throws LogicException
     */
    public function getErrorResult(): ErrorResult
    {
        if ($this->isOk()) {
            throw new LogicException('Result is not ERROR');
        }

        return $this->result;
    }
}
