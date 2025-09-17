<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SifItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sif_id',
        'item_code',
        'description',
        'uom',
        'quantity',
    ];

    public function sif()
    {
        return $this->belongsTo(Sif::class);
    }
}
