<?php

namespace NeverThrow;

abstract class Result
{
    private bool $isOk;
    protected readonly mixed $okResult;
    protected readonly mixed $errorResult;

    public static function ok(mixed $okResult): self
    {
        $result = new static();
        $result->isOk = true;
        $result->okResult = $okResult;

        return $result;
    }

    public static function error(mixed $errorResult): self
    {
        $result = new static();
        $result->isOk = false;
        $result->errorResult = $errorResult;

        return $result;
    }

    public function isOk(): bool
    {
        return $this->isOk;
    }

    public function isError(): bool
    {
        return !$this->isOk;
    }

    public function getOkResult(): mixed
    {
        return $this->okResult ?? null;
    }

    public function getErrorResult(): mixed
    {
        return $this->errorResult ?? null;
    }
}
