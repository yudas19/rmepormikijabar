<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPoliSatusehat extends Model
{
    protected $table = 'master_poli_satusehats';

    protected $guarded = ['id'];

    public function masterPoli()
    {
        return $this->belongsTo(Poli::class, 'master_poli_id');
    }
}
