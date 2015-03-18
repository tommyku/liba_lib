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

    /**
     * @dataProvider getAvailableRoomsProvider
     * @testdox Should be able to get available rooms and timeslots
     */
    public function testgetAvailableRooms($bauth_user, $bauth_pass, $date, $area, $cover, $limit)
    {
        $schedule = new LibaAPI\Schedule($bauth_user, $bauth_pass);

        $res = $schedule->getAvailable($date, $area, $cover, $limit);

        $this->assertEquals($area, $res['area']);
        $this->assertEquals($date, $res['date']);
        $this->assertEquals($cover, $res['cover']);
        $this->assertEquals($limit, $res['limit']);
        $this->assertLessThanOrEqual($limit, count($res['timeslots']));
    }

    public function isAvailableWithoutLoginFProvider()
    {
        return [[
            base64_decode('username'),
            base64_decode('password'),
            new DateTime('2015-03-12 15:00:00'),
            new DateTime('2015-03-12 17:00:00'),
            4,
            19
        ]];
    }

    public function isAvailableWithoutLoginTProvider()
    {
        return [[
            base64_decode('username'),
            base64_decode('password'),
            new DateTime('2015-03-12 16:30:00'),
            new DateTime('2015-03-12 17:00:00'),
            4,
            19
        ]];
    }

    public function getAvailableRoomsProvider()
    {
        return [[
            base64_decode('username'),
            base64_decode('password'),
            (new DateTime())->modify('1 hour'),
            4,
            100,
            50
        ]];
    }

}