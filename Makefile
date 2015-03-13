UNIT=phpunit --color

test-all: 
	$(UNIT)

test-parser:
	$(UNIT) ParserTest tests/ParserTest.php

test-exception:
	$(UNIT) ExceptionsTest tests/ExceptionsTest.php
