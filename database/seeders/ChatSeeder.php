<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chat;

class ChatSeeder extends Seeder
{
    public function run()
    {
        Chat::query()->delete(); // safer than truncate

        Chat::create([
            'user1_id' => 1,
            'user2_id' => 2,
        ]);
    }
}
