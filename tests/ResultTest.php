<?php

namespace NeverThrow\Tests;

use LogicException;
use NeverThrow\ErrorResult;
use NeverThrow\Result;
use NeverThrow\SuccessResult;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testOkResult()
    {
        $okResult = new TransferResult($transferOkResult = new TransferOkResult('#999302'));

        $this->assertTrue($okResult->isOk());
        $this->assertFalse($okResult->isError());
        $this->assertSame($transferOkResult, $okResult->getOkResult());
    }

    public function testOkResultGetErrorResultThrowsAnException()
    {
        $this->expectException(LogicException::class);

        $okResult = new TransferResult(new TransferOkResult('#999302'));

        $okResult->getErrorResult();
    }

    public function testErrorResult()
    {
        $errorResult = new TransferResult($transferErrorResult = new TransferErrorResult(TransferErrorOutcome::INSUFFICIENT_BALANCE));

        $this->assertFalse($errorResult->isOk());
        $this->assertTrue($errorResult->isError());
        $this->assertSame($transferErrorResult, $errorResult->getErrorResult());
    }

    public function testErrorResultGetOkResultThrowsAnException()
    {
        $this->expectException(LogicException::class);

        $result = new TransferResult(new TransferErrorResult(TransferErrorOutcome::INSUFFICIENT_BALANCE));

        $result->getOkResult();
    }
}

class TransferOkResult extends SuccessResult
{
    public function __construct(
        public string $transferId
    ) {
    }
}

enum TransferErrorOutcome
{
    case INSUFFICIENT_BALANCE;
    case BENEFICIARY_INCORRECT;
}

class TransferErrorResult extends ErrorResult
{
    public function __construct(
        public TransferErrorOutcome $outcome
    ) {
    }
}

class TransferResult extends Result
{
    public function getOkResult(): TransferOkResult
    {
        return parent::getOkResult();
    }

    public function getErrorResult(): TransferErrorResult
    {
        return parent::getErrorResult();
    }
}
