<?php

namespace LibaAPI\Exceptions;

class RoomNotExistException extends \Exception
{
    public function __construct($message, $code=0, Exception $previous=NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}