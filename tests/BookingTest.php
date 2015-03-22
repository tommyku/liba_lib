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

    public function isBookableProvider()
    {
        return [[
            base64_decode('username'),
            base64_decode('password '),
            (new DateTime)->modify('+1 hours'),
            (new DateTime)->modify('+2 hours'),
            4,
            19
        ]];
    }

}