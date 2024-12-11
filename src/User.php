<?php

namespace MarghoobSuleman\HashtagCms;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MarghoobSuleman\HashtagCms\Core\Traits\RoleManager;
use MarghoobSuleman\HashtagCms\Models\Site;
use MarghoobSuleman\HashtagCms\Models\UserProfile;
use MarghoobSuleman\HashtagCms\Models\Role;
use MarghoobSuleman\HashtagCms\Models\UserSite;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, RoleManager;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_user');
    }
}
