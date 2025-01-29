<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Time;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_criar_time()
    {
        $csrfResponse = $this->getJson('/csrf-token');
        $csrfToken = $csrfResponse->json('token');

        $response = $this->postJson('/times', ['nome' => 'Time A'], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('times', ['nome' => 'Time A']);
    }

    public function test_limite_de_8_times()
    {
        $csrfResponse = $this->getJson('/csrf-token');
        $csrfToken = $csrfResponse->json('token');

        Time::factory()->count(8)->create();

        $response = $this->postJson('/times', ['nome' => 'Time I'], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'O número máximo de times (8) já foi atingido.']);
    }
}