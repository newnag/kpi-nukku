<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); // PK, Auto Increment
            $table->string('title', 255)->nullable(); // ชื่อการแจ้งเตือน
            $table->integer('day_notify')->nullable(); // จำนวนวันล่วงหน้า
            $table->date('notify_date')->nullable(); // วันที่จะแจ้งเตือน
            $table->string('message', 500)->nullable(); // ข้อความแจ้งเตือน
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
