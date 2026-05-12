<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdmissionStudent extends Model
{
    use HasFactory;

    protected $fillable = [

        'school_id',
        // Step 1
        'division',
        'district',
        'upazila',
        'school',
        'class',
        'group',
        'section',
        'session',
        'admission_fee',
        'admission_date',

        // Step 2
        'previous_school',
        'previous_class',
        'previous_group',
        'previous_section',
        'previous_session',
        'interview_code',
        'last_exam_result',

        // Step 3
        'guardian_id',

        // Step 4
        'student_name',
        'father_name',
        'mother_name',
        'student_id_number',

        'current_division',
        'current_district',
        'current_upazila',
        'current_village',

        'permanent_division',
        'permanent_district',
        'permanent_upazila',
        'permanent_village',

        'mobile',
        'password',
        'image',
        'status'
    ];


    protected static function booted()
    {
        static::creating(function ($model) {
            // Generate a sequential student ID number starting from 00000001
            $lastStudent = static::orderBy('id', 'desc')->first();
            
            if ($lastStudent && $lastStudent->student_id_number) {
                // Extract the numeric part and increment
                $lastNumber = (int)$lastStudent->student_id_number;
                $newNumber = $lastNumber + 1;
            } else {
                // Start from 1 if no records exist
                $newNumber = 1;
            }
            
            // Format to 8 digits with leading zeros
            $model->student_id_number = str_pad($newNumber, 8, '0', STR_PAD_LEFT);
        });

        static::created(function ($model) {
            //
        });
    }

    // Relationship: Student belongs to Guardian
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class', 'id');
    }

    public function schoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section', 'id');
    }

    public function schoolGroup()
    {
        return $this->belongsTo(SchoolGroup::class, 'group', 'id');
    }

    public function schoolSession()
    {
        return $this->belongsTo(SchoolSession::class, 'session', 'id');
    }



}
