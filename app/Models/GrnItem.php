<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class GrnItem extends Model
{
    protected $fillable = ['grn_id','item_code','description','uom','quantity'];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }
}
