<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => User::factory(),
            'body' => $this->faker->paragraph(),
            'attachment_path' => null,
            'read_at' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (): array => [
            'read_at' => now(),
        ]);
    }

    public function withAttachment(): static
    {
        return $this->state(fn (): array => [
            'attachment_path' => 'messages/'.$this->faker->uuid().'.'.$this->faker->fileExtension(),
        ]);
    }
}
