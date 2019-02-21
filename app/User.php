<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cartalyst\Sentinel\Users\EloquentUser;
use Sentinel;
use Illuminate\Support\Facades\Storage;

class User extends EloquentUser
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'last_name',
        'first_name',
        'profilephoto',
        'permissions',
        'company_name',
        'timezone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $loginNames = ['email', 'username'];

    public function clients()
    {
        return $this->hasMany('App\Clients', 'user_id');
    }

    public function getFullNameAttribute()
    {

        $fullName = [];

        if ( ! empty($this->first_name))
        {
            $fullName[] = $this->first_name;
        }

        if ( ! empty($this->last_name))
        {
            $fullName[] = $this->last_name;
        }

        if (empty($fullName))
        {
            $name = $this->email;
        }
        else
        {
            $name = implode(' ', $fullName);
        }

        return $name;

    }

    public function getProfilePhotoAttribute()
    {

        if ( ! empty($this->attributes['profilephoto']))
        {
            return Storage::url('public/user/profile/' . $this->attributes['profilephoto']);
        } else {
            return asset('assets/images/profile_default.png');
        }

    }

}
