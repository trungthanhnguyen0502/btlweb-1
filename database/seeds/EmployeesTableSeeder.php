<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            'id' => 1,
            'email' => 'thanhtung.uet@gmail.com',
            'password' => md5('12345678'),
            'gender' => 0,
            'birthday' => strtotime('Nov 1, 1997'),
            'first_name' => 'Thanh Tùng',
            'last_name' => 'Phạm',
            'display_name' => 'Phạm Thanh Tùng',
            'title' => 'Developer',
            'team_id' => 1,
            'is_leader' => 1,
            'role' => 3,
            'role_title' => 'Department Manager'
        ]);

        DB::table('employees')->insert([
            'id' => 2,
            'email' => 'trungthanhnguyen0502@gmail.com',
            'password' => md5('12345678'),
            'gender' => 0,
            'birthday' => strtotime('Feb 05, 1997'),
            'first_name' => 'Thành Trung',
            'last_name' => 'Nguyễn',
            'display_name' => 'Nguyễn Thành Trung',
            'title' => 'Developer',
            'team_id' => 1,
            'is_leader' => 1,
            'role' => 3,
            'role_title' => 'Department Manager'
        ]);

        DB::table('employees')->insert([
            'id' => 3,
            'email' => 'theiron97@gmail.com',
            'password' => md5('12345678'),
            'gender' => 0,
            'birthday' => strtotime('Feb 05, 1997'),
            'first_name' => 'Đình Tuân',
            'last_name' => 'Nguyễn',
            'display_name' => 'Nguyễn Đình Tuân',
            'title' => 'Developer',
            'team_id' => 1,
            'is_leader' => 1,
            'role' => 3,
            'role_title' => 'Department Manager'
        ]);
    }
}
