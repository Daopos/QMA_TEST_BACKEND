<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classlist extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'class_id'];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    // public function classroom() {
    //     return $this->belongsTo(Classroom::class);
    // }

    public function classroom() {
        return $this->belongsTo(Classroom::class, 'class_id');
    }
}