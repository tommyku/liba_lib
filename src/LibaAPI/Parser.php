<?php

namespace LibaAPI;

class Parser
{
    protected static $areas = [3, 10, 4, 6, 8];

    // singleton pattern
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    // singleton pattern
    protected function __construct() {}

    // singleton pattern
    private function __clone() {}

    // singleton pattern
    private function __wakeup() {}

    /**
     * Parse available room schedule for the next 2 week for all area
     *
     * @param string $_bAuth_user
     * @param string $_bAuth_pass
     * @return array Array date with array of area, containing array of rooms and timeslots
     */
    public static function parseSchedule($_bAuth_user = NULL, $_bAuth_pass = NULL)
    {
        // all day, all area, all room
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }
    }

    /**
     * One day, all area, all room
     *
     * @param string $_bAuth_user
     * @param string $_bAuth_pass
     * @param DateTime $date
     * @return array Array of area, containing array of rooms and timeslots
     */
    public static function parseDay($_bAuth_user = NULL, $_bAuth_pass = NULL, $date)
    {
        // bauth check done by parseArea
        $data = [
            'date' => $date,
            'areas' => []
        ];
        foreach(self::$areas as $area) {
            $data['areas'][strval($area)] = self::parseArea($_bAuth_user, $_bAuth_pass, $date, $area);
        }

        return $data;
    }

    /**
     * One day, one area, one room
     *
     * @param string $_bAuth_user
     * @param string $_bAuth_pass
     * @param DateTime $date
     * @param string $area
     * @param string $room
     * @return array Array of available timeslots
     */
    public static function parseRoom($_bAuth_user = NULL, $_bAuth_pass = NULL, $date, $area, $room)
    {
        // bauth check done by parseArea
        $areaSchedule = self::parseArea($_bAuth_user, $_bAuth_pass, $date, $area);
        $roomName = strval($room);
        // TODO: the key should be changed to room id just parseDay
        foreach ($areaSchedule['rooms'] as $val) {
            if ($val['room'] == $roomName) {
                return $val;
            }
        }

        throw new RoomNotExistException('Room not in this area?');
    }


    /**
     * One day, one area, all room
     *
     * @param DateTime $date
     * @param string $area
     * @return array Array of rooms and timeslots in the given area
     */
    public static function parseArea($_bAuth_user = NULL, $_bAuth_pass = NULL, $date, $area)
    {
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }

        $url = self::buildScheduleURL($date, $area);
        $schedule = self::loadSchedule($_bAuth_user, $_bAuth_pass, $url);

        $data = [
            'area' => $area,
            'date' => $date,
            'rooms' => []
        ];

        // find the room steps from thead
        $dom = str_get_dom($schedule);
        // run through the tbody
        $timeHead = self::getTimeHeadByArea($area);
        $roomCount = count($dom('table[id="day_main"] > thead > tr > th'))-2;
        $slotsCount = self::getSlotsCountByArea($area);
        $trs = $dom('table[id="day_main"] > tbody > tr');


        $ths = $dom('table[id="day_main"] > thead > tr > th');
        for ($i=1; $i<$roomCount+1; ++$i) {
            $th = $ths[$i];
            $data['rooms'][] = [
                'room' => $th->{'data-room'},
                'timeslots' => []
            ];
        }

        // O(n^2) mapping
        // fill in 1/0 first
        $grid = array_fill(0, $roomCount, array_fill(0, $slotsCount, 0));
        $skip = array_fill(0, $roomCount, 0); // no skip initially

        foreach ($trs as $rm => $tr) {
            $head = 1; // head for grabbing td from tr
            foreach ($skip as $key => $val) {
                if ($val > 1) { // skipping
                    --$skip[$key];
                } else {
                    // new timeslot or existing appt, see the td
                    $td = $tr('td', $head);
                    ++$head;
                    // existing appt?
                    if (strpos($td->class, 'new') === false) {
                        // echo $td->html();
                        $skip[$key] = $td->rowspan;
                        for ($i=$rm; $i<$rm+$td->rowspan; ++$i) {
                            $grid[$key][$i] = 1;
                        }
                    }
                }
            }
        }

        // generate timeslots from $grid
        // run through thead to get all room names first < this is for parseSchedule
        foreach ($data['rooms'] as $steps => $room) {
            $slotHead = NULL;
            foreach ($grid[$steps] as $time => $val) {
                if ($val === 1) {
                    if ($slotHead !== NULL) {
                        // close it
                        $data['rooms'][$steps]['timeslots'][] = [
                            'start' => ($slotHead+$timeHead),
                            'end' => ($time+$timeHead),
                            'duration' => $time-$slotHead
                        ];
                        $slotHead = NULL;
                    }
                } else {
                    if ($slotHead === NULL) {
                        $slotHead = $time;
                    }
                }
            }

            if ($slotHead !== NULL) {
                // no time head, we guess from $grid
                $data['rooms'][$steps]['timeslots'][] = [
                    'start' => ($slotHead+$timeHead),
                    'end' => ($slotsCount+$timeHead),
                    'duration' => $slotsCount-$slotHead
                ];
            }
        }

        return $data;
    }

    /**
     * Parse booking schedule for the next 2 week for all area
     *
     * @param string $_bAuth_user
     * @param string $_bAuth_pass
     * @return void
     */
    public static function parseBookingSchedule($_bAuth_user = NULL, $_bAuth_pass = NULL)
    {
        // all day, all area, all room
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }
    }

    private static function loadSchedule($_bAuth_user, $_bAuth_pass, $url)
    {
        $headers = [];
        $options = ['auth' => [$_bAuth_user, $_bAuth_pass]];
        $request = \Requests::get($url, [], $options);
        switch ($request->status_code) {
        case 401:
            throw new Exceptions\UnauthorizedException('Wrong username or password when loading schedule');
        default:
            return $request->body;
        }
    }

    private static function buildScheduleURL($date, $area)
    {
        if (!is_a($date, 'DateTime')) {
            throw InvalidArgumentException('Method buildURL accepts only DateTime as first parameter');
        }

        $baseURL = 'http://lbbooking.ust.hk/calendar/day.php?';
        $parameters = [
            'year' => $date->format('Y'),
            'month' => $date->format('m'),
            'day' => $date->format('j'),
            'area' => intval($area),
        ];
        return $baseURL.http_build_query($parameters);
    }

    private static function buildBookingURL($date, $area, $room)
    {
        if (!is_a($date, 'DateTime')) {
            throw InvalidArgumentException('Method buildURL accepts only DateTime as first parameter');
        }
        $baseURL = 'http://lbbooking.ust.hk/calendar/edit_entry.php?';
        $parameters = [
            'year' => $date->format('Y'),
            'month' => $date->format('m'),
            'day' => $date->format('j'),
            'area' => intval($area),
            'room' => intavl($room),
            'hour' => $date->format('g'),
            'minute' => $date->format('i'),
        ];
        return $baseURL.http_build_query($parameters);
    }

    private static function buildBookingDetailsURL($date, $area, $room, $id)
    {
        if (!is_a($date, 'DateTime')) {
            throw InvalidArgumentException('Method buildURL accepts only DateTime as first parameter');
        }

        $baseURL = 'http://lbbooking.ust.hk/calendar/view_entry.php?';
        $parameters = [
            'year' => $date->format('Y'),
            'month' => $date->format('m'),
            'day' => $date->format('j'),
            'area' => intval($area),
            'id' => intval($id)
        ];
        return $baseURL.http_build_query($parameters);
    }

    private static function getTimeHeadByArea($area)
    {
        switch (intval($area)) {
        case 3;
        case 10:
        case 4:
        case 6:
            return 16;
            break;
        case 8:
        default:
            return 0;
        }
    }

    private static function getSlotsCountByArea($area)
    {
        switch (intval($area)) {
        case 3;
        case 10:
        case 4:
        case 6:
            return 30;
            break;
        case 8:
        default:
            return 48;
        }
    }

    private static function findRoomSteps($dom, $room)
    {
        $steps = -1;
        $day_main = $dom('table[id="day_main"] > thead > tr:first-child > th'); // first row
        foreach ($day_main as $i => $el) {
            $a = $el('a');
            if (count($a) && strpos($a[0]->href, 'room='.$room) !== false) {
                $steps = $i;
                break;
            }
        }
        if ($steps == -1) {
            throw Exceptions\RoomNotExistException('Room '.$room.' not in this area '.$area.'?');
        }
        return $steps-1;
    }
}