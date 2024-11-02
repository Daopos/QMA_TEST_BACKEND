<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'teacher_id',
        'classroom_id',
        'start',
        'end',
        'day',
        'archived',
    ];

    public function classroom()
{
    return $this->belongsTo(Classroom::class);
}
}