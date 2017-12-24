<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $request_time = time();

        for ($i = 1; $i < 100; $i++) {
            DB::table('tickets')->insert([
                'id' => $i,
                'subject' => "Công việc thứ {$i}",
                'content' => "Nội dung mẫu cho công việc thứ {$i}",
                'created_by' => mt_rand(1, 3),
                'status' => mt_rand(1, 6),
                'out_of_date' => mt_rand(1, 10) % 2 + 1,
                'priority' => mt_rand(1, 5),
                'deadline' => date('Y-m-d H:i:s', $request_time + mt_rand(1000, 9999)),
                'assigned_to' => mt_rand(0, 3),
                'attachment' => 0,
                'team_id' => mt_rand(1, 10) % 2 + 1,
            ]);
        }
    }
}
