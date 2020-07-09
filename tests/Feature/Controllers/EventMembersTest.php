<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;

class EventMembersTest extends TestCase
{

    public function testUnauthorizedIndexAction()
    {
        $response = $this->get('/api/members');
        $response->assertStatus(401);
    }

    public function testIndexAction()
    {
        $response = $this->withHeader('Bearer', env('API_KEY'))->get('/api/members');
        $response->assertStatus(200)->assertJsonFragment(['email' => 'ivanovp@bgs.test']);
    }

    public function testIndexActionWithFilter()
    {
        $response = $this->withHeader('Bearer', env('API_KEY'))->get('/api/members?event=2');
        $response->assertStatus(200)
            ->assertJsonFragment(['email' => 'egorove@bgs.test'])
            ->assertJsonMissing(['email' => 'ivanovp@bgs.test']);
//        $response->dump();
    }

    public function testStoreActionValidators()
    {
        $badData = [
            [
                'surname' => 'Doe',
                'email' => 'test@email.local',
                'event_id' => 1,
            ],
            [
                'name' => 'John',
                'email' => 'test@email.local',
                'event_id' => 1,
            ],
            [
                'name' => 'John',
                'surname' => 'Doe',
                'event_id' => 1,
            ],
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'egorove@bgs.test',
                'event_id' => 1,
            ],
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'test@email.local',
            ],
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'test@email.local',
                'event_id' => 15,
            ],
        ];
        foreach ($badData as $data) {
            $response = $this->withHeader('Bearer', env('API_KEY'))->postJson('/api/members', $data);
            $response->assertStatus(400)->assertJsonFragment(['status' => 'error']);
        }
    }

    public function testStoreAction()
    {
        $response = $this->withHeader('Bearer', env('API_KEY'))->postJson('/api/members', [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'test@email.local',
            'event_id' => 1,
        ]);
        $response->assertStatus(200)->assertJsonFragment(['email' => 'test@email.local']);
    }
}
