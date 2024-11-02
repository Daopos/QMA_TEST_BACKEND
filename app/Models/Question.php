<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['test_id', 'type', 'question_text', 'choices', 'correct_answer','title','index_position', 'pts'];


    protected $casts = [
        'choices' => 'array', // Cast choices to an array
        'correct_answers' => 'array', // Cast correct_answers to an array
    ];

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}