<?php

namespace Gamepanelio\SpigetApi\Exception;

class ApiCommunicationException extends \RuntimeException
{
    /**
     * @param \Exception $exception
     * @return static
     */
    public static function wrap(\Exception $exception)
    {
        return new static($exception->getMessage(), $exception->getCode(), $exception);
    }
}
