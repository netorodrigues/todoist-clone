<?php

namespace App\Exceptions;

use Exception;

class APIException extends Exception
{
    private $details;

    public function __construct(string $message, array $details)
    {
        parent::__construct($message, 400);

        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }
}
