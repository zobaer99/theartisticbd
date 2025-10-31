<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingLocationPrice extends Model
{
    protected $fillable = [
        'title', 'division_id', 'district_id', 'thana_id', 'price', 'status'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }
}
