<?php
class ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider parseAreaProvider
     * @testdox Parser should be able to get area schedule
     */
    public function testParseArea($bauth_user, $bauth_pass, $date, $area)
    {
        $res = LibaAPI\Parser::parseArea($bauth_user, $bauth_pass, $date, $area);
        $this->assertArrayHasKey('area', $res);
        $this->assertEquals($res['area'], intval($area));
        $this->assertArrayHasKey('date', $res);
        $this->assertEquals($res['date'], $date);
        $this->assertArrayHasKey('rooms', $res);
        $this->assertEquals(count($res['rooms']), 3);
    }

    /**
     * @dataProvider parseRoomProvider
     * @testdox Parser should be able to get room schedule
     */
    public function testParseRoom($bauth_user, $bauth_pass, $date, $area, $room)
    {
        $res = LibaAPI\Parser::parseRoom($bauth_user, $bauth_pass, $date, $area, $room);
        $this->assertArrayHasKey('room', $res);
        $this->assertArrayHasKey('timeslots', $res);
        $this->assertEquals(2, count($res));
        $this->assertEquals(3, count($res['timeslots']));
        $this->assertEquals(16, $res['timeslots'][0]['start']);
        $this->assertEquals(18, $res['timeslots'][0]['end']);
        $this->assertEquals(29, $res['timeslots'][1]['start']);
        $this->assertEquals(30, $res['timeslots'][1]['end']);
    }

    /**
     * @dataProvider parseDayProvider
     * @testdox Parser should be able to get room schedules of all areas in a day
     */
    public function testParseDay($bauth_user, $bauth_pass, $date)
    {
        $res = LibaAPI\Parser::parseDay($bauth_user, $bauth_pass, $date);
        $this->assertArrayHasKey('date', $res);
        $this->assertEquals($res['date'], $date);
        $this->assertArrayHasKey('areas', $res);
        $this->assertEquals(5, count($res['areas']));
        $this->assertArrayHasKey(3, $res['areas']);
        $this->assertArrayHasKey(10, $res['areas']);
        $this->assertArrayHasKey(4, $res['areas']);
        $this->assertArrayHasKey(6, $res['areas']);
        $this->assertArrayHasKey(8, $res['areas']);
    }

    public function parseRoomProvider()
    {
        return [[
            base64_decode(''),
            base64_decode(''),
            new DateTime('2015-03-12 15:00:00'),
            3,
            28
        ]];
    }

    public function parseAreaProvider()
    {
        return [[
            base64_decode(''),
            base64_decode(''),
            new DateTime('2015-03-12 15:00:00'),
            4
        ]];
    }

    public function parseDayProvider()
    {
        return [[
            base64_decode(''),
            base64_decode(''),
            new DateTime('2015-03-12 15:00:00')
        ]];
    }
}
