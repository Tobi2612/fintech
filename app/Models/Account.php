<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'first_name',
        'account_number',
        'account_balance',
        'account_type',
        'username',
        'email',
        'pin',
        'user_id'
    ];

    protected $hidden = [
        'pin',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function transaction_history(){
        return $this->hasMany(Account::class,'account_id');
    }

    
}
