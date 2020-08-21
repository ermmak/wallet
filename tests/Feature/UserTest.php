<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test 422
     *
     * @return void
     */
    public function testStore422()
    {
        $this->json('POST', 'api/users', ['Accept' => 'application/json'])
            ->assertStatus(422);
    }

    /**
     * Test successfull store
     */
    public function testStoreSuccessFully()
    {
        $this
            ->json(
                'POST',
                'api/users',
                factory(User::class)->raw(),
                [ 'Accept' => 'application/json' ]
            )
            ->assertOk();
    }

    /**
     * Assert user data
     */
    public function testGetUser()
    {
        $this
            ->json('GET', 'api/users/1', [ 'Accept' => 'application/json' ])
            ->assertJsonStructure([
                'name', 'email', 'city'
            ]);
    }
}
