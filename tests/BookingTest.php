<?php
class BookingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider isBookableProvider
     * @testdox Should be able to check if a room is bookable from the library system
     */
    public function testisBookable($bauth_user, $bauth_pass, $start, $end, $area, $room)
    {
        $booking = new LibaAPI\Booking($bauth_user, $bauth_pass); // no login data provided
        $res = $booking->isBookable($start, $end, $area, $room);
        $this->assertObjectHasAttribute('valid_booking', $res);
        $this->assertObjectHasAttribute('rules_broken', $res);
        $this->assertObjectHasAttribute('conflicts', $res);
    }

    /**
     * @dataProvider isNotBookableProvider
     * @testdox Should be able to check if a room is not bookable from the library system
     */
    public function testisNotBookable($bauth_user, $bauth_pass, $start, $end, $area, $room)
    {
        $booking = new LibaAPI\Booking($bauth_user, $bauth_pass); // no login data provided
        $res = $booking->isBookable($start, $end, $area, $room);
        $this->assertObjectHasAttribute('valid_booking', $res);
        $this->assertEquals(false, $res->valid_booking);
        $this->assertObjectHasAttribute('rules_broken', $res);
        $this->assertObjectHasAttribute('conflicts', $res);
    }

    /**
     * @dataProvider isNotBookableProvider
     * @testdox Should get a RoomNotBookableException when trying to book an unbookable room
     * @expectedException LibaAPI\Exceptions\RoomNotBookableException
     */
    public function testtryBookingNotBookable($bauth_user, $bauth_pass, $start, $end, $area, $room)
    {
        $booking = new LibaAPI\Booking($bauth_user, $bauth_pass); // no login data provided
        $res = $booking->book($start, $end, $area, $room);
    }


    /**
     * @dataProvider isBookableProvider
     * @testdox Should be able to book a room
     */
    public function testtryBookingARoom($bauth_user, $bauth_pass, $start, $end, $area, $room)
    {
        $booking = new LibaAPI\Booking($bauth_user, $bauth_pass); // no login data provided
        $res = $booking->book($start, $end, $area, $room);
        $this->assertEquals(true, $res);
    }

    public function isBookableProvider()
    {
        return [[
            base64_decode('username'),
            base64_decode('password'),
            (new DateTime)->modify('+6 hours'),
            (new DateTime)->modify('+7 hours'),
            4,
            19
        ]];
    }

    public function isNotBookableProvider()
    {
        return [[
            base64_decode('username'),
            base64_decode('password'),
            new DateTime('2015-03-23 10:30:00'),
            new DateTime('2015-03-23 12:30:00'),
            4,
            19
        ]];
    }

}