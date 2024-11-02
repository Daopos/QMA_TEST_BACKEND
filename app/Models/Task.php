<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;


    protected $fillable = [
        "subject_id",
        "teacher_id",
        "title",
        "description",
        "file",
        "due_date"
    ];
}