<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'title' => 'Московская выставка',
                'date_start' => '2020-07-25 14:30:00',
                'city_id' => 1
            ],
            [
                'id' => 2,
                'title' => 'Московская выставка 2',
                'date_start' => '2020-07-26 14:30:00',
                'city_id' => 1
            ],
            [
                'id' => 3,
                'title' => 'Воронежский съезд',
                'date_start' => '2020-07-20 14:30:00',
                'city_id' => 2
            ],
            [
                'id' => 4,
                'title' => 'Концерт в Самаре',
                'date_start' => '2020-07-18 14:30:00',
                'city_id' => 3
            ]
        ];
        DB::table('events')->insert($data);
    }
}
