<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject_id',
        'description',
        'status',
        'deadline',
        'score',

    ];

    public function studentClassworks()
    {
        return $this->hasMany(StudentClasswork::class, 'submission_id');
    }

    public function submissions()
    {
        return $this->hasMany(ClassworkSubmission::class, 'classwork_id');
    }

    public static function updateStatusBasedOnDeadline()
    {
        // Get all classworks where the deadline has passed
        $classworks = self::where('deadline', '<', now())
            ->where('status', '<>', 'close')
            ->get();

        // Update their status to 'closed'
        foreach ($classworks as $classwork) {
            $classwork->status = 'close';
            $classwork->save();
        }
    }


}
