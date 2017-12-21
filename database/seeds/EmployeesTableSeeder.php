<?php

use Illuminate\Database\Seeder;

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
            'role' => 5,
            'role_title' => 'Department Manager'
        ]);
    }
}
