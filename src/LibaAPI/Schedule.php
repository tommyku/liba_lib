<?php
namespace LibaAPI;

class Schedule
{
    protected $bAuth_user = "";
    protected $bAuth_pass = "";

    public function __construct($_bAuth_user = NULL, $_bAuth_pass = NULL)
    {
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }

        $this->bAuth_user = $_bAuth_user;
        $this->bAuth_pass = $_bAuth_pass;
    }

    public function query($date, $area, $cover)
    {
    }

    public function getAvailable($date, $area, $limit)
    {
    }

    public function isAvailable($date, $area, $room)
    {
    }

    public static function load()
    {
        return "Hello world\n";
    }
}