<?php

namespace Tests\Feature\Http\Controllers\Api\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use Carbon\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreatesUser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;

    public function testInvoke_正常系()
    {
        $lesson = \App\Models\Lesson::factory()->create();
        $user = $this->createUser();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");
        $response->assertStatus(201);
        $response->assertJson([
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }

    public function testInvoke_異常系()
    {
        $lesson = \App\Models\Lesson::factory()->create(['capacity' => 1]);
        $lesson->reservations()->save(\App\Models\Reservation::factory()->make());
        $user = $this->createUser();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");
        $response->assertStatus(409);
        $response->assertJsonStructure(['error']);

        //メッセージの中身まで確認したい場合
        $error = $response->json('error');
        $this->assertStringContainsString('予約できません。', $error);

        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }
}
