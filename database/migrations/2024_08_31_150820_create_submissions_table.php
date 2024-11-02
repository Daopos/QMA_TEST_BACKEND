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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->string('file_path')->nullable(); // Path to submitted file
            $table->enum('status', ['submitted', 'graded', 'late']);
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
