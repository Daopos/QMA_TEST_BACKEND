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
        Schema::create('classwork_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classwork_id')->constrained('classworks')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->timestamps();
        });

        Schema::create('student_classworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('classwork_submissions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade'); // This line is retained for clarity but may be redundant
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classwork_submissions');
    }
};
