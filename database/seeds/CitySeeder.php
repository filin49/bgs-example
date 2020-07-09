<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
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
                'title' => 'Moscow'
            ],
            [
                'id' => 2,
                'title' => 'Voronezh'
            ],
            [
                'id' => 3,
                'title' => 'Samara'
            ]
        ];
        DB::table('cities')->insert($data);
    }
}
