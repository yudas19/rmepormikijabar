<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientReferral extends Model
{
    protected $table = 'patient_referrals';

    protected $guarded = ['id'];

    protected $casts = [
        'is_tacc' => 'boolean',
        'diagnosa_sekunder' => 'array',
        'response_json' => 'array',
    ];
}
