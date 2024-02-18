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
        Schema::create('Attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employeeID')->references('id')->on('Employee')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('userID')->nullable()->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->string('type');
            $table->integer('date');
            $table->string('time');
            $table->integer('created_at');
            $table->integer('updated_at');

            $table->unique(['employeeID', 'date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Attendance');
    }
};
