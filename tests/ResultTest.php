<?php

namespace NeverThrow\Tests;

use NeverThrow\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testOkResult()
    {
        $okResult = TransferResult::ok(
            $transferOkResult = new TransferOkResult('#999302')
        );

        $this->assertTrue($okResult->isOk());
        $this->assertFalse($okResult->isError());
        $this->assertSame($transferOkResult, $okResult->getOkResult());
        $this->assertNull($okResult->getErrorResult());
    }

    public function testErrorResult()
    {
        $errorResult = TransferResult::error(
            $transferErrorResult = new TransferErrorResult(TransferErrorOutcome::INSUFFICIENT_BALANCE)
        );

        $this->assertFalse($errorResult->isOk());
        $this->assertTrue($errorResult->isError());
        $this->assertSame($transferErrorResult, $errorResult->getErrorResult());
        $this->assertNull($errorResult->getOkResult());
    }
}

class TransferOkResult
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

class TransferErrorResult
{
    public function __construct(
        public TransferErrorOutcome $outcome
    ) {
    }
}

/**
 * @method TransferOkResult getOkResult()
 * @method TransferErrorResult getErrorResult()()
 */
class TransferResult extends Result
{
}
