<?php

use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        DB::table('teams')->insert([
            'id' => 1,
            'title' => 'Hà Nội',
            'description' => 'Hà Nội IT'
        ]);

        DB::table('teams')->insert([
            'id' => 2,
            'title' => 'Đà Nẵng',
            'description' => 'Đà Nẵng IT'
        ]);
    }
}
