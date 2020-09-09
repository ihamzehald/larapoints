<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(@OA\Xml(name="User"))
 *
 * @OA\Property(
 *   property="id",
 *   type="string",
 *   description="User ID"
 * )
 *
 * @OA\Property(
 *   property="name",
 *   type="string",
 *   description="User name"
 * )
 *
 * @OA\Property(
 *   property="email",
 *   type="string",
 *   description="User email"
 * )
 *
 * @OA\Property(
 *   property="email_verified_at",
 *   type="string",
 *   description="Email verified at"
 * )
 *
 * @OA\Property(
 *   property="created_at",
 *   type="string",
 *   description="Created at"
 * )
 *
 * @OA\Property(
 *   property="updated_at",
 *   type="string",
 *   description="Updated at"
 * )
 *
 */

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
        return [];
    }
}
