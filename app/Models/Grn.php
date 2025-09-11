<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    use HasFactory;

    protected $fillable = [
        'lpo_id','lpo_no','supplier_name','date','supplier_code',
        'requested_by','inv_no','department','inv_date','project_name'
    ];

    // GRN ke items
    public function items(){
        return $this->hasMany(GrnItem::class);
    }

    // GRN ka LPO
    public function lpo(){
        return $this->belongsTo(Lpo::class);
    }
}
