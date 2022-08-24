<?php

namespace App\Models;

use App\Traits\SoftDeleteCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    use HasFactory,SoftDeleteCustom;

    protected $table = 'car_brands';

    protected $guarded = [];
}
