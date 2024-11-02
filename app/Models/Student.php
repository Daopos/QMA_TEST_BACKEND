<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Student extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lrn',
        'track',
        'strand',
        'surname',
        'firstname',
        'middlename',
        'extension_name',
        'street',
        'barangay',
        'municipality',
        'province',
        'birthdate',
        'nationality',
        'birth_municipality',
        'birth_province',
        'gender',
        'religion',
        'contact',
        'email',
        'social_media',
        'father_name',
        'father_occupation',
        'father_contact',
        'father_social',
        'mother_name',
        'mother_occupation',
        'mother_contact',
        'mother_social',
        'guardian_name',
        'guardian_occupation',
        'guardian_contact',
        'guardian_social',
        'previous_school_name',
        'previous_school_address',
        'birth_certificate',
        'report_card',
        'transcript_record',
        'good_moral',
        'enrolment_status',
        'password',
        'grade_level',
        'student_fee',
        'image',
        'mother_email',
        'guardian_email',
        'father_email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function fees()
    {
        return $this->hasMany(GradeFee::class, 'student_id');
    }

    public function classlists() {
        return $this->hasMany(Classlist::class);
    }

    public function enrollments()
{
    return $this->hasMany(Enrollment::class, 'student_id');
}

}
