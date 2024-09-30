<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmailVerificationToken extends Model
{
    public $timestamps = false;
     protected $fillable=['token','expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
     }
    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->greaterThan($this->expires_at);
    }
}
