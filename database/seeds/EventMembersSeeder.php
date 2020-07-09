<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventMembersSeeder extends Seeder
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
                'name' => 'Петр',
                'surname' => 'Иванов',
                'email' => 'ivanovp@bgs.test',
                'event_id' => 1
            ],
            [
                'id' => 2,
                'name' => 'Иван',
                'surname' => 'Петров',
                'email' => 'petrovi@bgs.test',
                'event_id' => 1
            ],
            [
                'id' => 3,
                'name' => 'Егор',
                'surname' => 'Егоров',
                'email' => 'egorove@bgs.test',
                'event_id' => 2
            ],
            [
                'id' => 4,
                'name' => 'Инокентий',
                'surname' => 'Инокентьев',
                'email' => 'inokentyevi@bgs.test',
                'event_id' => 3
            ]
        ];
        DB::table('event_members')->insert($data);
    }
}
