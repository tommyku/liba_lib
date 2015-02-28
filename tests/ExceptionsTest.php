<?php

require_once __DIR__.'/../vendor/autoload.php';

use LibaAPI\Exceptions;

$e = new Exceptions\UnauthorizedException('username or password is wrong');

throw $e;