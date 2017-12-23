<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Get current employee info
     *
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

    /**
     * Search employee by name
     *
     * @param Request $request
     * @return array
     */

    public function search_employee(Request $request)
    {
        if ($request->has('name')) {
            $name = $request->input('name');
            $employees = DB::table('employees')
                ->select('id', 'email', 'first_name', 'last_name', 'display_name')
                ->where('display_name', 'LIKE', "%{$name}%")
                ->get();

            return $employees;
        }

        return [];
    }

}

