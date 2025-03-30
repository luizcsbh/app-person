<?php

namespace App\Exceptions;

use Exception;

class DatabaseException extends Exception
{
    protected $code = 500;
    
    public function __construct($message = "Database operation failed")
    {
        parent::__construct($message, $this->code);
    }
}