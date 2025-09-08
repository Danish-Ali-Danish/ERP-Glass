<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lpo_id','description','area','quantity','uom','unit_price','total'
    ];

    public function lpo(){
        return $this->belongsTo(Lpo::class);
    }
}
