<?php

namespace MarghoobSuleman\HashtagCms;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use MarghoobSuleman\HashtagCms\Core\Traits\RoleManager;
//use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\HasApiTokens;
use MarghoobSuleman\HashtagCms\Models\UserProfile;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, RoleManager;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = array();

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * with profile
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile() {
        return $this->hasOne(UserProfile::class);
    }

}
