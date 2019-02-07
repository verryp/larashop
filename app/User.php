<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass as, 'statussignable.'
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 
        'roles', 'address', 'phone', 'status', 'province_id', 'city_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    // * Fungsi buat generate random api_token
    public function generateToken(){
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }
}
