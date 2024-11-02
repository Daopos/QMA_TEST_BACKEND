<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'grade_level',
        'adviser_id',
        'archived',
    ];

    public function classLists() {
        return $this->hasMany(ClassList::class);
    }
}