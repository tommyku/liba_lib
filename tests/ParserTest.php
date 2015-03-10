<?php
ini_set('xdebug.var_display_max_depth', -1 );

require_once __DIR__.'/../vendor/autoload.php';

include __DIR__.'/config.php';

$dt = new DateTime('2015-03-10 15:00:00');

use LibaAPI\Parser;

echo "Parsing room: \n";

var_dump(LibaAPI\Parser::parseRoom($config['bAuth.user'],$config['bAuth.pass'], $dt, 3, 28));

echo "Parsing area: \n";

var_dump(LibaAPI\Parser::parseArea($config['bAuth.user'],$config['bAuth.pass'], $dt, 10));

echo "Parsing day: \n";

var_dump(LibaAPI\Parser::parseDay($config['bAuth.user'],$config['bAuth.pass'], $dt));
