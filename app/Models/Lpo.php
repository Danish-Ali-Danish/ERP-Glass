<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpo extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name','date','contact_person','lpo_no',
        'contact_no','pi_no','supplier_trn','address',
        'sub_total','vat','net_total'
    ];

    public function items(){
        return $this->hasMany(LpoItem::class);
    }
     

    // App/Models/Lpo.php
public function grns()
{
    return $this->hasMany(Grn::class);
}

}
