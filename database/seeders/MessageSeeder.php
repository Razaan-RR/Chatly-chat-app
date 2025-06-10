<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    public function run()
    {
        Message::query()->delete(); // safer than truncate

        Message::create([
            'chat_id' => 1,
            'user_id' => 1,
            'body' => 'Hi Bob, how are you?',
        ]);

        Message::create([
            'chat_id' => 1,
            'user_id' => 2,
            'body' => 'Hey Alice! I am good, thanks. How about you?',
        ]);
    }
}
