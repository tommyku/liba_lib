<?php

require_once __DIR__.'/../vendor/autoload.php';

$e = new LibaAPI\Exceptions\UnauthorizedException('username or password is wrong');

throw $e;