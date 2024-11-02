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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('teacher_id')->nullable()->references('id')->on('employees')->onDelete('cascade')->nullable();
            $table->foreignId('classroom_id')->nullable()->references('id')->on('classrooms')->onDelete('cascade');
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->string('day')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
