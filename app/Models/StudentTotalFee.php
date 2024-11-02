<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTotalFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_fee',
    ];

}