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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->string('grade_level')->nullable(); // Grade level during enrollment
            $table->string('enrollment_status')->nullable(); // e.g., 'enrolled', 'withdrawn'
            $table->boolean('archived')->default(false); // Whether the enrollment is archived or not
            $table->timestamps();

            // Foreign key relations
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
