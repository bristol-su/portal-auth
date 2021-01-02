<?php


namespace BristolSU\Auth\Exceptions;


use Throwable;

class EmailNotVerified extends \Exception
{

    public function __construct($message = 'Email verification required', $code = 423, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
