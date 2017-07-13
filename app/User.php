<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App
 *
 * @property boolean    $can_unbundle
 * @property boolean    $can_allocate
 * @property boolean    $can_repack
 * @property boolean    $can_enroll
 * @property boolean    $is_active
 * @property boolean    $is_admin
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const TABLE_NAME = 'npvn_users';

    protected $table = self::TABLE_NAME;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'can_unbundle'      => $this->can_unbundle,
            'can_enroll'        => $this->can_enroll,
            'can_allocate'      => $this->can_allocate,
            'can_repack'        => $this->can_repack,
            'is_admin'          => $this->is_admin,
            'is_active'         => $this->is_active,
        ];
    }
}
