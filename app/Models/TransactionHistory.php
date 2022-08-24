<?php

namespace App\Models;

use App\Traits\SoftDeleteCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory,SoftDeleteCustom;

    protected $table = 'transaction_histories';

    protected $guarded = [];

    public function wallet()
    {
        return $this->belongsTo('App\Models\Wallet','wallet_id','id');
    }
}
