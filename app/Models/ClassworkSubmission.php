<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassworkSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'score',
        'classwork_id',
        'file',
    ];


    // Relationships

    public function studentClassworks()
    {
        return $this->hasMany(StudentClasswork::class, 'submission_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classwork()
    {
        return $this->belongsTo(Classwork::class);
    }


}
