<?php

namespace App\Shopify;

use App\Models\User;
use Illuminate\Support\Facades\Auth as BaseAuth;

class Auth
{

    /**
     * Generates a sanctum access token for a user
     *
     * @param User|null $user
     * @param array     $grants
     *
     * @return string|null
     */
    public function getAccessTokenForUser( ?User $user = null, array $grants = ['*'] ){
        if( ! $user = $user ?? BaseAuth::user() ){
            return null;
        }
        if( $apiToken = $user?->createToken('shopify', $grants, now()->addHours(12 ) )?->plainTextToken ){
            return explode('|', $apiToken, 2)[1] ?? null;
        }
        return null;
    }
}
