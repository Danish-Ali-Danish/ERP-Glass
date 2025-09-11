<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sif extends Model
{
    protected $fillable = [
        'sif_no','date','issued_date','requested_by','department','project_name','remarks'
    ];

    public function items()
    {
        return $this->hasMany(SifItem::class);
    }
}
