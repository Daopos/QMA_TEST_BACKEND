<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'answers',
        'is_done',
        'test_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class, 'submission_id');
    }
}