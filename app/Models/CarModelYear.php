<?php

namespace App\Models;

use App\Traits\SoftDeleteCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModelYear extends Model
{
    use HasFactory,SoftDeleteCustom;

    protected $table = 'car_model_years';

    protected $guarded = [];

    public function car()
    {
        return $this->belongsTo('App\Models\Car','car_id','id');
    }
}
