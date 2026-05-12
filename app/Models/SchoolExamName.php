<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExamName extends Model
{
    protected $fillable = [
        'school_id', 
        'class_name', 
        'group_name', 
        'session_name', 
        'exam_name', 
        'section_name'
    ];
}