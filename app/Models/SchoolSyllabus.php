<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSyllabus extends Model
{
    use HasFactory;

    
    protected $table = 'school_syllabuses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'school_id',
        'session_id',
        'class_id',
        'group_id',
        'section_id',
        'subject_id',
        'exam_name',
        'start_page',
        'end_page'
    ];

    /**
     * Get the academic session associated with the syllabus.
     */
    public function school_session()
    {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    /**
     * Get the class associated with the syllabus.
     */
    public function school_class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the group associated with the syllabus.
     */
    public function school_group()
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    /**
     * Get the section associated with the syllabus.
     */
    public function school_section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    /**
     * Get the subject associated with the syllabus.
     */
    public function school_subject()
    {
        return $this->belongsTo(SchoolSubject::class, 'subject_id');
    }
}
