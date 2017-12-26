<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AsyncMailer {

    /**
     * HTTP Request Header
     * @var array
     */

    private $req_header;

    // Set header line
    public function set_header($key, $value = '') {
        if ($value == '') {
            unset($this->req_header[$key]);
        } else {
            $this->req_header[$key] = $value;
        }
    }

    /**
     * HTTP Response Header
     * include in the output
     * @var boolean
     */

    private $res_header;

    // Include the header in the output
    public function show_header() {
        $this->res_header = True;
    }

    // Exclude the header from the output
    public function hide_header() {
        $this->res_header = False;
    }

    /**
     * The value of "User-Agent:" header line
     *
     * @var string
     */

    private $user_agent;

    // Set User Agent for the requests
    public function set_user_agent($user_agent = null) {
        if ($user_agent == null) {
            if ($_SERVER['HTTP_USER_AGENT'])
                $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
            else {
                $this->user_agent =
                    'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/64.4.146 Chrome/58.4.3029.146 Safari/537.36';
            }
        }
    }

    /**
     * The file to save cookies
     *
     * @var string
     */

    private $cookie_file;

    // Set the Cookie file to store cookie data
    public function set_cookie_file($cookie_file) {
        if (is_file($cookie_file)) {
            $this->cookie_file = $cookie_file;
        } else {
            $this->cookie_file = '';
        }
    }

    /**
     * The value of "Referer:" header line
     *
     * @var string
     */

    private $referer;

    // Set Referer for the Curl requests
    public function set_referer($referer = '') {
        $this->referer = $referer;
    }


    /**
     * The Request Timeout
     * (in seconds)
     *
     * @var integer
     */

    private $timeout;

    // Set timeout for all requests
    public function set_timeout($timeout = 100) {
        $this->timeout = $timeout;
    }

    /**
     * Class constructor,
     * initial variables.
     */

    public function __construct() {

        $this->req_header = array();
        $this->set_header('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');
        // Exclude header from output by default
        $this->hide_header();
        // Default User-Agent
        $this->set_user_agent();
        // Empty Referer
        $this->set_referer('');
        // No cookie file
        $this->set_cookie_file('');
        // Timeout: 100 secs
        $this->set_timeout(100);
    }

    /**
     * Perform a request
     *
     * @param   string
     * @param   array
     * @param   string
     * @return  string
     */

    public function exec($url, $vars = array(), $cookie = '') {

        // Initial Curl session
        $ch = curl_init($url);

        // Set options
        curl_setopt($ch, CURLOPT_HTTPHEADER,    $this->req_header);
        curl_setopt($ch, CURLOPT_HEADER,        $this->res_header);
        curl_setopt($ch, CURLOPT_USERAGENT,     $this->user_agent);

        // Set the referer for the request
        if ($this->referer) {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }

        // Set timeout for the request
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        // Ignore SSL verification
        if (strncmp($url, 'https://', 8) == 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, False);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, False);
        }

        // Send cookie string
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }

        // Set using cookie in file
        if ($this->cookie_file) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->cookie_file);
        }

        // The POST data
        if (! empty($vars)) {
            curl_setopt($ch, CURLOPT_POST, True);
            $post_fields = http_build_query($vars);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        // Return the Response (or Error)
        if ($error) {
            return $error;
        } else {
            return $response;
        }
    }

    public function get_cookies($response) {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        return $cookies;
    }
}