<?php

namespace App\Models;

use App\Traits\SoftDeleteCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory,SoftDeleteCustom;

    protected $table = 'wallet';

    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany('App\Models\TransactionHistory','wallet_id','id');
    }
}
