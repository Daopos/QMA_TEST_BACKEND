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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->references('id')->on('test_submissions')->onDelete('cascade');
            $table->foreignId('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->nullable(); // Optional: indicates if the answer is correct
            $table->integer('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};