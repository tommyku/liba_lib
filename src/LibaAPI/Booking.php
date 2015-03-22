<?php
namespace LibaAPI;

class Booking
{
    /**
     * @var string              $bAuth_user ITSC username for basic auth login
     * @var string              $bAuth_pass ITSC password for basic auth login
     * @var Requests_Cookie_Jar $cookie     Cookie to store the session key after login
     */
    protected $bAuth_user;
    protected $bAuth_pass;
    protected $cookie;

    public function __construct($_bAuth_user = NULL, $_bAuth_pass = NULL)
    {
        if ($_bAuth_user === NULL || $_bAuth_pass === NULL) {
            throw InvalidArgumentException('Schedule class constructor only accept non-null username and password');
        }

        $this->bAuth_user = $_bAuth_user;
        $this->bAuth_pass = $_bAuth_pass;
        $this->cookie = NULL;

        $this->login();
    }

    public function book($start, $end, $area, $room)
    {
        $check = $this->isBookable($start, $end, $area, $room);
        if (!$check->valid_booking) {
            throw new Exceptions\RoomNotBookableException(json_encode($check));
        }

        // book it
        $baseURL = 'http://lbbooking.ust.hk/calendar/edit_entry_handler.php';
        $headers = [];
        $options = [
            'auth' => [$this->bAuth_user, $this->bAuth_pass],
            'follow_redirects' => false
        ];
        $data = [
            'name' => $this->bAuth_user,
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
            'rep_id' => '1',
            'edit_type' => 'series'
        ];

        $headers = [
            'Origin' => 'http://lbbooking.ust.hk',
            'X-Requested-With' => 'XMLHttpRequest',
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.76 Safari/537.36',
            'Referer' => 'http://lbbooking.ust.hk/calendar/edit_entry.php?'
        ];
        $type = 'POST';

        $this->cookie->before_request($baseURL, $headers, $data, $type, $options);
        $request = \Requests::post($baseURL, $headers, $data, $options);
        switch ($request->status_code) {
        case 200:
            return false;
            break;
        case 302:
            return true;
            break;
        default:
            return false;
            break;
        }
    }

    public function isBookable($start, $end, $area, $room)
    {
        // then get the result
        $baseURL = 'http://lbbooking.ust.hk/calendar/edit_entry_handler.php';
        $headers = [];
        $options = ['auth' => [$this->bAuth_user, $this->bAuth_pass]];
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
            'type' => '',
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
        $type = 'POST';

        $this->cookie->before_request($baseURL, $headers, $data, $type, $options);
        $request = \Requests::post($baseURL, $headers, $data, $options);

        if ($request->status_code == 401) {
            throw new Exceptions\UnauthorizedException('the user is not authorized in library system');
        }

        if ($this->isJson($request->body)) {
            return json_decode($request->body);
        } else {
            throw new \Exception('json decode error, response goes banana?');
        }
    }

    private function login()
    {
        // get the cookie
        $headers = [];
        $options = ['auth' => [$this->bAuth_user, $this->bAuth_pass]];
        $baseURL = 'http://lbbooking.ust.hk/calendar/edit_entry.php';
        $request = \Requests::get($baseURL, $headers, $options);
        if ($request->status_code == 401) {
            throw new Exceptions\UnauthorizedException('the user is not authorized in library system');
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
        if ($request->status_code == 401) {
            throw new Exceptions\UnauthorizedException('the user is not authorized in library system');
        }

        // put the logined session into cookie of this instance
        $this->cookie = $cookie;
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    private function datetime2second($date)
    {
        return intval($date->format('G'))*3600+intval($date->format('i'))*60+intval($date->format('s'));
    }
}