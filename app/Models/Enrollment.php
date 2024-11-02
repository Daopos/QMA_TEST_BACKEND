<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'grade_level',
        'enrollment_status',
        'archived',

    ];

    public function student()
{
    return $this->belongsTo(Student::class, 'student_id');
}
}
