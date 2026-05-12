<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolExamRoutine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id',
        'exam_date',
        'day_name',
        'start_time',
        'end_time',
        'total_hours',
        'class_name',
        'group_name',
        'section_name',
        'session_name',
        'exam_name',
        'subject_name',
    ];

    /**
     * Relationship with the School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}