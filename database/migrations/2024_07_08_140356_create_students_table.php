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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('lrn')->nullable();
            $table->string('track')->nullable();
            $table->string('strand')->nullable();
            $table->string('surname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('extension_name')->nullable();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('birthdate')->nullable();
            $table->string('nationality')->nullable();
            $table->string('birth_municipality')->nullable();
            $table->string('birth_province')->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('social_media')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_contact')->nullable();
            $table->string('father_social')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_contact')->nullable();
            $table->string('mother_social')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->string('guardian_contact')->nullable();
            $table->string('guardian_social')->nullable();
            $table->string('previous_school_name')->nullable();
            $table->string('previous_school_address')->nullable();
            $table->boolean('birth_certificate')->nullable();
            $table->boolean('report_card')->nullable();
            $table->boolean('transcript_record')->nullable();
            $table->boolean('good_moral')->nullable();
            $table->string('enrolment_status')->nullable(); // Optional if you want to store the overall status here
            $table->string('password')->nullable();
            $table->string('grade_level')->nullable();
            $table->string('student_fee')->nullable();
            $table->string('image')->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('father_email')->nullable();
            $table->string('mother_email')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
