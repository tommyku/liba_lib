UNIT=phpunit --color

test-all: 
	$(UNIT)

test-parser: tests/ParserTest.php
	$(UNIT) ParserTest tests/ParserTest.php

test-exception: tests/ExceptionsTest.php
	$(UNIT) ExceptionsTest tests/ExceptionsTest.php

test-schedule: tests/ScheduleTest.php
	$(UNIT) ScheduleTest tests/ScheduleTest.php

test-booking: tests/BookingTest.php
	$(UNIT) BookingTest tests/BookingTest.php
