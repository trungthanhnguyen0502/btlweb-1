<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeApiController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */

    public function get_employee_info(Request $request) {
        if ($request->session()->has('login_key')) {

            $employee = Employee::find($request->session()->get('employee_id'));
            unset($employee->password);

            return $employee;
        }

        return response('{}')->header('Content-Type', 'application/json');
    }
}
