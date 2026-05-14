<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            // Get the school's id_number (5-digit school code)
            $school = \App\Models\School::where('user_id', $model->school_id)->first();
            
            if (!$school || !$school->id_number) {
                // If school doesn't have an id_number, generate one starting from 00001
                $lastSchool = \App\Models\School::orderBy('id', 'desc')->first();
                if ($lastSchool && $lastSchool->id_number && is_numeric($lastSchool->id_number)) {
                    $lastCode = (int)$lastSchool->id_number;
                    $newCode = $lastCode + 1;
                } else {
                    $newCode = 1;
                }
                $schoolIdCode = str_pad($newCode, 5, '0', STR_PAD_LEFT);
                
                // Update the school with the new code
                if ($school) {
                    $school->id_number = $schoolIdCode;
                    $school->save();
                }
            } else {
                $schoolIdCode = $school->id_number;
            }
            
            // Get the last serial number for this specific school
            $lastStudent = static::where('school_id', $model->school_id)
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastStudent && $lastStudent->student_id_number && strlen($lastStudent->student_id_number) === 11) {
                // Extract the last 6 digits (serial part) and increment
                $lastSerial = substr($lastStudent->student_id_number, -6);
                $newSerial = (int)$lastSerial + 1;
            } else {
                // Start from 1 if no records exist for this school or ID format is different
                $newSerial = 1;
            }
            
            // Combine: 5-digit school code + 6-digit serial = 11-digit student ID
            $model->student_id_number = $schoolIdCode . str_pad($newSerial, 6, '0', STR_PAD_LEFT);
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
