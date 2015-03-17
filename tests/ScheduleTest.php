<?php
class ScheduleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider isAvailableWithoutLoginFProvider
     * @testdox Should be able to check room availablity without login (unavailable)
     */
    public function testisAvailableWithoutLoginF($bauth_user, $bauth_pass, $start, $end, $area, $room)
    {
        $schedule = new LibaAPI\Schedule($bauth_user, $bauth_pass); // no login data provided
        $res = $schedule->isAvailableWithoutLogin($start, $end, $area, $room);
        $this->assertEquals(false, $res);
    }

    /**
     * @dataProvider isAvailableWithoutLoginTProvider
     * @testdox Should be able to check room availablity without login (available)
     */
    public function testisAvailableWithoutLoginT($bauth_user, $bauth_pass, $start, $end, $area, $room)
    {
        $schedule = new LibaAPI\Schedule($bauth_user, $bauth_pass); // no login data provided
        $res = $schedule->isAvailableWithoutLogin($start, $end, $area, $room);
        $this->assertEquals(true, $res);
    }

    public function isAvailableWithoutLoginFProvider()
    {
        return [[
            base64_decode(''),
            base64_decode(''),
            new DateTime('2015-03-12 15:00:00'),
            new DateTime('2015-03-12 17:00:00'),
            4,
            19
        ]];
    }

    public function isAvailableWithoutLoginTProvider()
    {
        return [[
            base64_decode(''),
            base64_decode(''),
            new DateTime('2015-03-12 16:30:00'),
            new DateTime('2015-03-12 17:00:00'),
            4,
            19
        ]];
    }

}