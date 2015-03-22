<?php
class ExceptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException LibaAPI\Exceptions\InvalidDateException
     * @testdox Exception InvalidDateException should be available
     */
    public function testInvalidDateException()
    {
        throw new LibaAPI\Exceptions\InvalidDateException("");
    }

    /**
     * @expectedException LibaAPI\Exceptions\RoomNotExistException
     * @testdox Exception RoomNotExistException should be available
     */
    public function testRoomNotExistException()
    {
        throw new LibaAPI\Exceptions\RoomNotExistException("");
    }

    /**
     * @expectedException LibaAPI\Exceptions\UnauthorizedException
     * @testdox Exception UnauthorizedException should be available
     */
    public function testUnauthorizedException()
    {
        throw new LibaAPI\Exceptions\UnauthorizedException("");
    }

    /**
     * @expectedException LibaAPI\Exceptions\RoomNotBookableException
     * @testdox Exception RoomNotBookableException should be available
     */
    public function testRoomNotBookableException()
    {
        throw new LibaAPI\Exceptions\RoomNotBookableException("");
    }

}