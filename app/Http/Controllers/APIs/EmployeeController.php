<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */

    public function get_employee_info(Request $request)
    {
        if ($request->session()->has('login_key')) {

            $employee = Employee::find($request->session()->get('employee_id'));
            unset($employee->password);

            return $employee;
        }

        return response('{}')->header('Content-Type', 'application/json');
    }

    public function search_employee(Request $request)
    {
        if ($request->has('name')) {
            $name = $request->input('name');

            $employees = DB::table('employees')
                ->select('id', 'email', 'first_name', 'last_name', 'display_name')
//                ->where('email', 'LIKE', "{$name}%")
//                ->orWhere('first_name', 'LIKE', "%{$name}%")
//                ->orWhere('last_name', 'LIKE', "%{$name}%")
                ->where('display_name', 'LIKE', "%{$name}%")
                ->get();

            return $employees;
        }

        return [];
    }

}
