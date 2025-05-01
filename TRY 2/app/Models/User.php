<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted() {
        static::creating(function ($model) {
        $model->uuid = Str::uuid()->toString();
        });
   }

   /**
   * 
   * @return type 
   *  listing order By Desc
   */
    public function stripePlan() {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }
    
    /**
     * stripeUser
     *
     * @return void
     */
    public function stripeUser(){
        return $this->hasOne(StripeUser::class,'user_id','id');
    }
    
}
