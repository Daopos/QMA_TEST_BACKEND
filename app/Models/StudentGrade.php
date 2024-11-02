<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'subject',
        'first_quarter',
        'second_quarter',
        'third_quarter',
        'fourth_quarter',
        'academic_year',
        'grade_level',
    ];
}