<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_name',
        'division',
        'district',
        'upazila',
        'village',
        'id_number',
        'eiin_number',
        'mobile',
        'email',
        'logo',
        'sms_balance', // New field for SMS balance
    ];

    // Relationship: School belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(SchoolSubscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
