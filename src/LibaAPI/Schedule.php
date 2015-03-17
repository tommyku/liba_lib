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
        $schedule = Parser::parseRoom($start, $area, $room);
        $startTimeslot = $this->dateTime2timeslot($start);
        $endTimeslot = $this->dateTime2timeslot($end);
        foreach ($schedule['timeslots'] as $t) {
            if ($startTimeslot >= $t['start'] && $endTimeslot <= $t['end']) {
                return true;
            }
        }
        return false;
    }

    public function isAvailableWithLogin($start, $end, $area, $room)
    {
        if ($this->bAuth_user === '' || $this->bAuth_pass === '') {
            throw new \InvalidArgumentException('authorization details are not given');
        }

        // get the cookie
        $headers = [];
        $options = ['auth' => [$this->bAuth_user, $this->bAuth_pass]];
        $baseURL = 'http://lbbooking.ust.hk/calendar/edit_entry.php';
        $request = \Requests::get($baseURL, $headers, $options);
        if ($request->status == 401) {
            throw new Exceptions/UnauthorizedException('the user is not authorized in library system');
        }
        $cookie = $request->cookies; // it is a cookie jar

        // login first
        $data = [
            'NewUserName' => $this->bAuth_user,
            'NewUserPassword' => $this->bAuth_pass,
            'returl' => '',
            'TargetURL' => 'edit_entry.php?',
            'Action' => 'SetName'
        ];
        $options['follow_redirects'] = false;
        $type = 'POST';
        $cookie->before_request($baseURL, $headers, $data, $type, $options);
        $request = \Requests::post($baseURL, $headers, $data, $options);
        if ($request->status == 401) {
            throw new Exceptions/UnauthorizedException('the user is not authorized in library system');
        }

        // then get the result
        $baseURL = 'http://lbbooking.ust.hk/calendar/edit_entry_handler.php';
        $data = [
            'ajax' => '1',
            'name' => '',
            'description' => '',
            'start_day' => $start->format('j'),
            'start_month' => $start->format('m'),
            'start_year' => $start->format('Y'),
            'start_seconds' => $this->datetime2second($start),
            'end_day' => $end->format('j'),
            'end_month' => $end->format('m'),
            'end_year' => $end->format('Y'),
            'end_seconds' => $this->datetime2second($end),
            'area' => $area,
            'rooms[]' => $room,
            'type' => 'U',
            'returl' => 'http://lbbooking.ust.hk/calendar/edit_entry.php',
            'create_by' => $this->bAuth_user,
            'rep_id' => '0',
            'edit_type' => 'series'
        ];

        $headers = [
            'Origin' => 'http://lbbooking.ust.hk',
            'X-Requested-With' => 'XMLHttpRequest',
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.76 Safari/537.36',
            'Referer' => 'http://lbbooking.ust.hk/calendar/edit_entry.php?'
        ];
        $cookie->before_request($baseURL, $headers, $data, $type, $options);

        $type = 'POST';
        $cookie->before_request($baseURL, $headers, $data, $type, $options);
        $request = \Requests::post($baseURL, $headers, $data, $options);
        if ($request->status == 401) {
            throw new Exceptions/UnauthorizedException('the user is not authorized in library system');
        }

        return $request->body;
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