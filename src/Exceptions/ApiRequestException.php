<?php

namespace Sportmonks\SoccerAPI\Exceptions;

use RuntimeException;

class ApiRequestException extends RuntimeException
{
    public function __construct(string $message, int $status = 0, private readonly ?int $retryAfter = null)
    {
        parent::__construct($message, $status);
    }

    public function retryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
