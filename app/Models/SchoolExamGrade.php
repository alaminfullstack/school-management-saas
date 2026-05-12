<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolExamGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_name',
        'group_name',
        'section_name',
        'subject_name',
        'min_mark',
        'max_mark',
        'min_point',
        'letter_name',
        'number_point'
    ];
}
