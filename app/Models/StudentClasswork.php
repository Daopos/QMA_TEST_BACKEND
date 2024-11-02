<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentClasswork extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'student_id',
        'file',
    ];

    public function submission()
    {
        return $this->belongsTo(ClassworkSubmission::class, 'submission_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getFileUrlAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : null; // Generates the full URL
    }
}