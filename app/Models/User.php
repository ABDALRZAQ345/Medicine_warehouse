<?php

namespace App\Models;

use Laravel\Cashier\Billable;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles,HasApiTokens ,Billable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'roles',
        'remember_token',
    ];

   protected $casts = [
       'email_verified_at' => 'datetime:Y-m-d H:m:s',
       'created_at' => 'datetime:Y-m-d H:m:s',
       'updated_at' => 'datetime:Y-m-d H:m:s',
   ];

    public function Email_verification_tokens()
    {
        return $this->hasMany(EmailVerificationToken::class);
    }
    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class,'creator_id');
    }
    public function orders(){
        return $this->hasMany(Order::class,'orderer_id');
    }


///////
///
//
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->roles()->detach();
            $user->permissions()->detach();
            $user->tokens()->delete();
            $user->medicines()->detach();
            $user->orders()->detach();
            $user->Email_verification_tokens()->delete();
        });
//        static::created(function ($user) {
//            if (!Role::where('name', 'user')->exists())
//                Role::create(['name' => 'user']);
//            $user->assignRole('user');
//        });
    }



//
///
////
}
