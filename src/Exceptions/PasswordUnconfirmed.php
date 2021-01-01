<?php


namespace BristolSU\Auth\Exceptions;


use Throwable;

class PasswordUnconfirmed extends \Exception
{

    public function __construct($message = 'Password confirmation required', $code = 423, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
