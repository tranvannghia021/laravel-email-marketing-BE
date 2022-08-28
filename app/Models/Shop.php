<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Shop extends Authenticatable  implements JWTSubject
{
    use HasFactory;
    protected $table = 'shops';
    protected $fillable = [
        'name',
        'email',
        'shopify_domain',
        'status',
        'access_token',
        'hash_domain',
        'created_at',
        'updated_at'
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


    public function getAuthPassword()
    {
        return $this->hash_domain;
    }
}
