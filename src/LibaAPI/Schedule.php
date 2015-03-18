<?php
namespace LibaAPI;

class Schedule
{
    /**
     * @var string $bAuth_user ITSC username for basic auth login
     * @var string $bAuth_pass ITSC password for basic auth login
     */
    protected $bAuth_user;
    protected $bAuth_pass;

    public function __construct($_bAuth_user = NULL, $_bAuth_pass = NULL)
    {
        $this->bAuth_user = ($_bAuth_user === NULL) ? '' : $_bAuth_user;
        $this->bAuth_pass = ($_bAuth_pass === NULL) ? '' : $_bAuth_pass;
    }

    /**
     * Get a list of available room at the designated date time
     *
     * @param DateTime $date
     * @param int      $area
     * @param int      $cover
     * @param int      $limit
     * @return array Array of at most $limit timeslots available
     */
    public function getAvailable($date, $area=3, $cover=1, $limit=50)
    {
        $limit = ($limit <= 50) ? $limit : 50;

        $this->validateDate($date);

        $data = Parser::parseArea($this->bAuth_user, $this->bAuth_pass, $date, $area);
        // flatten it
        $timeslots = [];
        foreach($data['rooms'] as $room) {
            foreach($room['timeslots'] as $t) {
                $t['room'] = $room['room'];
                $timeslots[] = $t;
            }
        }

        $timeslots = array_slice($timeslots, 0, $limit);

        $rtn = [
            'area' => $area,
            'date' => $date,
            'cover' => $cover,
            'limit' => $limit,
            'timeslots' => $timeslots
        ];

        return $rtn;
    }

    /**
     * Check if a room is available without the need to login
     *
     * @param DateTime $start
     * @param DateTime $end
     * @param int      $area
     * @param int      $room
     * @return boolean Whether this room is available for booking at this timeslot
     */
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