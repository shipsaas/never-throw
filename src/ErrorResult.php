<?php

namespace NeverThrow;

abstract class ErrorResult implements ResultInterface
{
    public final function isOk(): false
    {
        return false;
    }
}
