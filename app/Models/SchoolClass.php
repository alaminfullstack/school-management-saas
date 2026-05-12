<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_name',
        'group',
        'section',
        'session',
        'roll',
        'student_id_number',
        'student_name',
        'total_fees',
        'fees_due'
    ];
}