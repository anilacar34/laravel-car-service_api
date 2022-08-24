<?php

namespace App\Models;

use App\Traits\SoftDeleteCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory,SoftDeleteCustom;

    protected $table = 'orders';

    protected $guarded = [];

    public function carModel()
    {
        return $this->belongsTo('App\Models\CarModelYear','model_id','id');
    }

    public function carService()
    {
        return $this->belongsTo('App\Models\CarService','service_id','id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Models\TransactionHistory','transaction_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','created_by','id');
    }
}
