<?php

namespace App\Http\Controllers\Auth;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    /**
     * @return string
     */

    public function index()
    {
        return response(view('auth.login')->with('title', 'Đăng nhập'));
    }

    /**
     * @param Request $request
     * @return $this|array
     */

    public function attempt(Request $request)
    {
        $email = $request->input('email');

        if ($email == '') {
            return redirect(route('login'));
        }

        $employee = Employee::where('email', $email)->get();

        if ($employee->count() == 0) {
            // If employee with this email does not exist
            return redirect(route('login'))
                ->withInput()
                ->withErrors(['email' => 'Tài khoản không tồn tại.']);
        } else {

            $password = $request->input('password');
            $password = md5($password);

            $employee = $employee[0];
            if ($employee->password != $password) {
                // If the input password does not match
                return redirect(route('login'))
                    ->withInput()
                    ->withErrors(['password' => 'Sai mật khẩu.']);
            } else {

                // Parse client's information
                $agent = new Agent();
                $request_time = $request->server('REQUEST_TIME');
                $ip_address = $request->server('REMOTE_ADDR');
                $browser = $agent->browser();
                $platform = $agent->platform();

                // Create login_key
                $login_key = $this->create_session_key($employee->id, $request_time, $ip_address);

                // Remember session for this client
                $request->session()->put('login_key', $login_key);
                $request->session()->put('employee_id', $employee->id);

                // Response
                $redirect = Redirect::to('/');
                if ($request->input('remember')) {

                    // Cookie Time To Live (minutes)
                    $cookie_ttl = 43200;

                    // Save session to database
                    $session = new Session();
                    $session->employee_id = $employee->id;
                    $session->login_key = $login_key;
                    $session->ip_address = $ip_address;
                    $session->browser = $browser;
                    $session->platform = $platform;
                    $session->expired_at = $request_time + $cookie_ttl * 60;
                    $session->save();

                    $redirect->withCookie(cookie()->forever('login_key', $login_key))
                        ->withCookie(cookie()->forever('employee_id', $employee->id))
                        ->withCookie(cookie()->forever('email', $employee->email));
                }

                return $redirect;
            }
        }
    }

    /**
     * @param   $employee_id  string
     * @param   $request_time integer
     * @param   $ip_address   string
     * @return string
     */

    protected function create_session_key($employee_id, $request_time, $ip_address)
    {
        $login_string = "{$employee_id}/{$request_time}/{$ip_address}";
        $hash = md5($login_string);
        $hash .= sha1($login_string);

        return $hash;
    }

    /**
     * @param Request $request
     * @return $this
     */

    public function logout(Request $request)
    {
        $login_key = $request->session()->get('login_key');
        // Delete session from database
        Session::where('login_key', $login_key)->delete();
        // Flush session data
        $request->session()->flush();
        // Delete cookie data and redirect
        return response()
            ->redirectToRoute('login')
            ->cookie('login_key', '', -1)
            ->cookie('employee_id', '', -1)
            ->cookie('email', '', -1);
    }
}