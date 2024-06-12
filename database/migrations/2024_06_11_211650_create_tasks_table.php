<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string("title")->index()->nullable(false);
            $table->string("content")->nullable(false);
            $table->integer("status_id")->index()->nullable(false);
            $table->string("attachment")->nullable(true);
            $table->integer("parent_id")->index()->nullable(true);
            $table->integer("user_id")->index()->nullable(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
