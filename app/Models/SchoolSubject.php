<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id', 
        'group_id', 
        'section_id', 
        'subject_name', 
    ];

    public function school_class() 
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function school_group() 
    {
        return $this->belongsTo(SchoolGroup::class, 'group_id');
    }

    public function school_section() 
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }
}