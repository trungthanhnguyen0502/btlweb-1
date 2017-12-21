<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ForgotPasswordController extends Controller
{
    /**
     * @return string
     */

    public function index()
    {
        return '';
    }

    protected function create_security_key($email, $request_time, $ip_address)
    {
        $rand_key = mt_rand(1000, 9999);
        $email = md5($email);
        $request_time = md5($request_time);
        $ip_address = md5($ip_address);

        return "{$rand_key}{$email}{$request_time}{$ip_address}";
    }

    public function
}
