<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferUser extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function refer_user()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }


    
}


