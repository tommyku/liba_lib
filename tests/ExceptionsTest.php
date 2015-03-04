<?php

require_once __DIR__.'/../vendor/autoload.php';

use LibaAPI\Exceptions;

try {
    throw new Exceptions\UnauthorizedException('Username or password is wrong');
} catch (Exceptions\UnauthorizedException $e) {
    echo "Caught UnauthorizedException: ".$e->getMessage()."\n";
}

try {
    throw new Exceptions\RoomNotExistException('Room not in this area?');
} catch (Exceptions\RoomNotExistException $e) {
    echo "Caught RoomNotExistException: ".$e->getMessage()."\n";
}
