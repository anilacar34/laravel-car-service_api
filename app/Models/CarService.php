<?php

namespace App\Models;

use App\Traits\SoftDeleteCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarService extends Model
{
    use HasFactory,SoftDeleteCustom;

    protected $table = 'car_services';

    protected $guarded = [];
}
