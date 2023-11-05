<?php

namespace NeverThrow;

abstract class SuccessResult implements ResultInterface
{
    public final function isOk(): true
    {
        return true;
    }
}
