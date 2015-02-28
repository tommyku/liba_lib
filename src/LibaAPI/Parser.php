<?php

namespace LibaAPI;

class Parser
{
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
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }

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
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }

        $url = self::buildScheduleURL($date, $area, $room);
        $schedule = self::loadSchedule($_bAuth_user, $_bAuth_pass, $url);

        $data = [
            'room' => $room,
            'timeslots' => []
        ];

        // find the room steps from thead
        // run through the tbody


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
            throw Exceptions\UnauthorizedException('Wrong username or password when loading schedule');
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
}