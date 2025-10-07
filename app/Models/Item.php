<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
     protected $fillable = [
        'item_code',
        'description',
        'uom',
        'size',
        'color',
        'type',
        'remarks',
    ];

        public function grn(){
        return $this->belongsTo(Grn::class);
    }
    public function quotationItems()
{
    return $this->hasMany(QuotationItem::class);
}


}

