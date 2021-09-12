<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_type',
        'purpose',
        'value',
        'account_id',
        'balance_before',
        'balance_after'
    ];


    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }
}
