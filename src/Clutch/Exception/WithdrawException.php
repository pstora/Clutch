<?php

namespace Clutch\Exception;

use Exception;

class WithdrawException extends Exception
{
    protected $errors;

    public function __construct($message, $errors = array())
    {
        $this->errors = $errors;

        parent::__construct($message);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
