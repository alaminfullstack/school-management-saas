<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'class_id', 'group_name', 'section', 'session', 
        'roll', 'student_id_number', 'student_name', 'total_payable', 'payable_due'
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}