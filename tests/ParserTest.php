<?php
ini_set('xdebug.var_display_max_depth', -1 );

require_once __DIR__.'/../vendor/autoload.php';

include __DIR__.'/config.php';

$dt = new DateTime('2015-03-04 15:00:00');

use LibaAPI\Parser;

var_dump(LibaAPI\Parser::parseRoom($config['bAuth.user'],$config['bAuth.pass'], $dt, 3, 11));
