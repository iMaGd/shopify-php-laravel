<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Shopify\Context;
use Shopify\Utils;

class SetContentSecurityPolicy
{
    /**
     * Ensures that the request is setting the appropriate CSP frame-ancestor directive.
     *
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle( Request $request, Closure $next )
    {
        $allowedDomains = "";

        if( $request->path() === 'exitiframe' ){
            $allowedDomains = "https://admin.shopify.com *.myshopify.com";
        }

        if( $request->attributes->get( 'shopifySession' ) ){
            $shop = $request->user()?->shopSession?->shop;

            if ( $shop && Context::$IS_EMBEDDED_APP ) {
                $shopDomain     = Utils::sanitizeShopDomain( $shop );
                $domainHost     = $shopDomain ? "https://$shopDomain" : "*.myshopify.com";
                $allowedDomains = "$domainHost https://admin.shopify.com";
            }
        }

        /** @var Response $response */
        $response = $next( $request );

        $currentHeader = $response->headers->get('Content-Security-Policy');
        if ($currentHeader) {
            $values = preg_split("/;\s*/", $currentHeader);

            // Replace or add the URLs the frame-ancestors directive
            $found = false;
            foreach ($values as $index => $value) {
                if (mb_strpos($value, "frame-ancestors") === 0) {
                    $values[$index] = preg_replace("/^(frame-ancestors)/", "$1 $allowedDomains", $value);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $values[] = "frame-ancestors $allowedDomains";
            }

            $headerValue = implode("; ", $values);
        } else {
            $headerValue = "frame-ancestors $allowedDomains;";
        }

        $response->headers->set('Content-Security-Policy', $headerValue);

        return $response;
    }
}
