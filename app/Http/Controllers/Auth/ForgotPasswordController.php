<?php

namespace App\Http\Controllers\Auth;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\PasswordResetKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Jenssegers\Agent\Agent;

class ForgotPasswordController extends Controller
{

    private $employee_does_not_exist = 'Tài khoản không tồn tại.';
    private $wrong_security_key = 'Mã xác thực không đúng.';
    private $invalid_password_length = 'Mật khẩu phải chứa 6-32 ký tự.';
    private $password_does_not_match = 'Mật khẩu nhập lại không khớp.';

    /**
     * @param Request $request
     * @return $this|string
     */

    public function request_password(Request $request)
    {
        $email = $request->input('email');

        if ($email == '') {

            return view('auth.passwords.request');
        } else {

            $captcha = $request->input("captcha");
            $captcha = md5($captcha);

            if ($captcha != $request->session()->get('captcha')) {

                return view('auth.passwords.request')
                    ->with('email', $email)
                    ->withErrors(['captcha' => 'Lỗi captcha.']);
            }

            $request->validate([
                'email' => 'email'
            ]);

            $employee = Employee::where('email', $email)->get();

            if ($employee->count() == 0) {
                return redirect(route('password.request'))
                    ->withInput()
                    ->withErrors(['email' => $this->employee_does_not_exist]);
            }

            $employee = $employee[0];

            $request_time = $request->server('REQUEST_TIME');
            $ip_address = $request->server('REMOTE_ADDR');
            $security_key = $this->create_security_key($employee->id);

            $reset_key = new PasswordResetKey();
            $reset_key->employee_id = $employee->id;
            $reset_key->security_key = $security_key;
            $reset_key->request_time = $request_time;
            $reset_key->requested_at = $request_time;
            $reset_key->expired_at = $request_time + 3600;
            $reset_key->ip_address = $ip_address;

            $agent = new Agent();
            $reset_key->browser = $agent->browser();
            $reset_key->platform = $agent->platform();

            $reset_key->save();

            // Send mail

            $mail = new PasswordReset();
            $mail->code = $security_key;
            $mail->reset_link = url(route('password.reset'));

            Mail::to($employee->email)->send($mail);

            return view('auth.passwords.request')
                ->with('success', true)
                ->with('email', $email);
        }
    }

    protected function create_security_key($employee_id)
    {
        $rand_key = mt_rand(100000, 999999);
        $rand_key = "{$employee_id}-{$rand_key}";
        return $rand_key;
    }

    public function reset_password(Request $request)
    {
        $security_key = $request->input('security_key');

        if ($security_key == '') {
            return view('auth.passwords.reset');
        }

        $request_time = $request->server('REQUEST_TIME');
        $reset_key = PasswordResetKey::
        where('security_key', $security_key)
            ->where('expired_at', '>', $request_time);

        if ($reset_key->count() == 0) {

            return view('auth.passwords.reset')
                ->with('security_key', $security_key)
                ->withErrors(['security_key' => $this->wrong_security_key]);
        }

        $password = $request->input('password');
        $password_length = strlen($password);

        if ($password_length < 6 || $password_length > 32) {
            return view('auth.passwords.reset')
                ->with('security_key', $security_key)
                ->withErrors(['password' => $this->invalid_password_length]);
        }

        $password_confirmation = $request->input('password_confirmation');

        if ($password != $password_confirmation) {
            return view('auth.passwords.reset')
                ->with('security_key', $security_key)
                ->withErrors(['password_confirmation' => $this->password_does_not_match]);
        }

        $reset_key = $reset_key->get()[0];

        Employee::where('id', $reset_key->employee_id)
            ->update(['password' => md5($password)]);
        PasswordResetKey::where('security_key', $security_key)->delete();

        return redirect(route('login'));
    }

}
