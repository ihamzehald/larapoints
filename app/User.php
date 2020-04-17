<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(@OA\Xml(name="User"))
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
 * @OA\Property(
 *   property="email",
 *   type="string",
 *   description="User email"
 * )
 * @OA\Property(
 *   property="email_verified_at",
 *   type="string",
 *   description="Email verified at"
 * )
 * @OA\Property(
 *   property="created_at",
 *   type="string",
 *   description="Created at"
 * )
 *  * @OA\Property(
 *   property="updated_at",
 *   type="string",
 *   description="Updated at"
 * )
 *  * @OA\Property(
 *   property="api_token",
 *   type="string",
 *   description="Api token (used for token auth)"
 * )
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
     * @author Hamza al darawsheh <ihamzehald@gmail.com>
     * @return mixed|string as api_token
     * Generates api_token as the access token to be used from the client side
     */
    public function generateToken()
    {
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }

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
