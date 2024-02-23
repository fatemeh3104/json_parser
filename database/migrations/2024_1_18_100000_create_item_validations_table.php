<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screen_item_id')->references('id')->on('screen_items')->onDelete('cascade')->onUpdate('cascade');
            $table->string('type');
            $table->json('validation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_validations');
    }
};
