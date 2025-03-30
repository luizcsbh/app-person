<?php

namespace App\Exceptions;

use Exception;

class InvalidFileException extends Exception
{
    protected $code = 422;
}