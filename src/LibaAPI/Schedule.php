<?php
namespace LibaAPI;

class Schedule
{
    protected $bAuth_user = '';
    protected $bAuth_pass = '';

    public function __construct($_bAuth_user = NULL, $_bAuth_pass = NULL)
    {
        $this->bAuth_user = ($_bAuth_user === NULL) ? $this->bAuth_user : $_bAuth_user;
        $this->bAuth_pass = ($_bAuth_pass === NULL) ? $this->bAuth_pass : $_bAuth_pass;
    }

    public function getAvailable($date, $area=3, $cover=1, $limit=50)
    {
        $limit = ($limit <= 50) ? $limit : 50;

        $this->validateDate($date);
    }

    public function isAvailableWithoutLogin($start, $end, $area, $room)
    {
        $this->validateBookingDuration($start, $end);
        $schedule = Parser::parseRoom($this->bAuth_user, $this->bAuth_pass, $start, $area, $room);
        $startTimeslot = $this->dateTime2timeslot($start);
        $endTimeslot = $this->dateTime2timeslot($end);
        foreach ($schedule['timeslots'] as $t) {
            if ($startTimeslot >= $t['start'] && $endTimeslot <= $t['end']) {
                return true;
            }
        }
        return false;
    }

    private function datetime2second($date)
    {
        return intval($date->format('G'))*3600+intval($date->format('i'))*60+intval($date->format('s'));
    }

    private function dateTime2timeslot($date)
    {
        return (intval($date->format('G'))*2 + (($date->format('i') == '00') ? 0 : 1));
    }

    private function validateDate($date)
    {
        $date->modify('midnight');
        $dateHead = new \DateTime();
        $dateTail = (new \DateTime('+2 weeks, +1 day'))->modify('midnight');

        if ($date < $dateHead || $date >= $dateTail) {
            throw new Exceptions\InvalidDateException('Date outside acceptable range');
        }
    }

    private function validateBookingDuration($start, $end)
    {
        if ($end <= $start) {
            throw new Exceptions\InvalidDateException('End date time should be earlier than start date time');
        }

        $start = (clone $start);
        $start->modify('+2 hours');
        if ($end > $start) {
            throw new Exceptions\InvalidDateException('Booking duration should be shorter or equal to 2 hours');
        }
    }

}