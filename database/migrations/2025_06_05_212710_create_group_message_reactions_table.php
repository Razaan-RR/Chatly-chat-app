<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMessageReactionsTable extends Migration
{
    public function up()
    {
        Schema::create('group_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_message_id')->constrained('group_messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reaction');
            $table->timestamps();

            $table->unique(['group_message_id', 'user_id']); // One reaction per user per message
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_message_reactions');
    }
}
