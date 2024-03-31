<?php

namespace App\Shopify;

use Illuminate\Support\Facades\Cookie;
use Shopify\Auth\OAuthCookie as BaseOAuthCookie;
use Shopify\Context;

class CookieHelper
{
    public static function saveShopifyCookie( BaseOAuthCookie $cookie ): bool
    {
        Cookie::queue(
            $cookie->getName(),
            $cookie->getValue(),
            $cookie->getExpire() ? ceil(( $cookie->getExpire() - time() ) / 60) : null,
            '/',
            parse_url(Context::$HOST_SCHEME . "://" . Context::$HOST_NAME, PHP_URL_HOST),
            $cookie->isSecure(),
            $cookie->isHttpOnly(),
            false,
            'Lax',
            true
        );

        return true;
    }

}
