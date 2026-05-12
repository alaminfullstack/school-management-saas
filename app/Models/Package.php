<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'package_type',
        'student_limit',
        'teacher_limit',
        'free_trial_days',
        'per_student_price',
        'total_payable',
        'annual_discount_percent',
        'after_discount',
        'sms_limit',
        'is_active'
    ];


    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class);
    }
}
