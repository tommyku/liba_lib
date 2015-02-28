<?php

require_once __DIR__.'/../vendor/autoload.php';

$dt = new DateTime('2014-02-27 15:00:00');

use LibaAPI\Parser;

var_dump(LibaAPI\Parser::parseRoom('', '', $dt, 3, 28));
