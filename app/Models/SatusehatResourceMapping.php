<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatusehatResourceMapping extends Model
{
    protected $table = 'satusehat_resource_mappings';

    protected $guarded = ['id'];

    public function resourcable()
    {
        return $this->morphTo('resourcable', 'resourcable_type', 'resourcable_id');
    }
}
