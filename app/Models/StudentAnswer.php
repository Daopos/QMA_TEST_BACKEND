<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;


    protected $fillable = [
        'submission_id',
        'question_id',
        'answer',
        'is_correct',
        "score",
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Define the relationship with the TestSubmission model
    public function submission()
    {
        return $this->belongsTo(TestSubmission::class);
    }
}