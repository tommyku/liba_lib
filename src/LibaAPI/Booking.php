<?php
namespace LibaAPI;

class Booking
{
    protected $bAuth_user;
    protected $bAuth_pass;
    protected $date;
    protected $area;

    public function __construct($_bAuth_user = NULL, $_bAuth_pass = NULL)
    {
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }

        $this->bAuth_user = $_bAuth_user;
        $this->bAuth_pass = $_bAuth_pass;
    }

}