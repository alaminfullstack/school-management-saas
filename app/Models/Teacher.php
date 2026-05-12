<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'designation',
        'id_number',
        'mobile',
        'email',
        'password',
        'dob',
        'photo',
    ];

    // Mutator for password hashing
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Relation to School
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
