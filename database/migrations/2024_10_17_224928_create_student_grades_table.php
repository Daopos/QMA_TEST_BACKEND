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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->references('id')->on('subjects')->onDelete('set null');
            $table->string('subject');
            $table->float('first_quarter')->nullable();
            $table->float('second_quarter')->nullable();
            $table->float('third_quarter')->nullable();
            $table->float('fourth_quarter')->nullable();
            $table->string('academic_year');
            $table->string('grade_level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};