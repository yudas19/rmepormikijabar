<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterIcd10 extends Model
{
    protected $table = 'master_icd10s';

    protected $guarded = ['id'];
}
