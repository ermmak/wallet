<?php

namespace Tests\Feature;

use Tests\TestCase;

class TransferTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testTransferPersisted()
    {
        $this->json(
            'POST',
            'api/transfers/make',
            [
                'from' => 1,
                'to' => 2,
                'amount' => 1000,
            ]
        );
        $this->assertDatabaseHas('transfers', ['amount' => 1000]);
    }

    public function testCannotTransferNegative()
    {
        $this
            ->json(
                'POST',
                'api/transfers/make',
                [
                    'from' => 1,
                    'to' => 2,
                    'amount' => -5,
                ]
            )
            ->assertStatus(422);
    }
}
