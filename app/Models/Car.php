<?php

namespace App\Models;

use App\Traits\SoftDeleteCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory,SoftDeleteCustom;

    protected $table = 'cars';

    protected $guarded = [];

    public function brand()
    {
        return $this->belongsTo('App\Models\CarBrand','brand_id','id');
    }

    public function years()
    {
        return $this->hasMany('App\Models\CarModelYear','car_id','id');
    }
}
