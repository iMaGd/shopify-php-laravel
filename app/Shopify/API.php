<?php

namespace App\Shopify;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class API
{
    /**
     * Get admin api url for an endpoint
     *
     * @param $shopDomain
     * @param $endpoint
     * @return string
     */
    public function getAdminEndpoint( $shopDomain, $endpoint ): string
    {
        return 'https://' . $shopDomain . '/admin/api/' . config( 'shopify.api_version' ) . '/' . $endpoint;
    }


    /**
     * Get shop details from shopify via access token
     *
     * @param $shopDomain
     * @param $accessToken
     *
     * @return false|mixed
     */
    public function getShopDetails( $shopDomain, $accessToken ): mixed
    {
        return $this->getAdmin( $shopDomain, 'shop.json', $accessToken );
    }

    /**
     * Retrieves data from admin api endpoint
     *
     * @param $shopDomain
     * @param $endpoint
     * @param $accessToken
     * @return false|mixed|null
     */
    public function getAdmin( $shopDomain, $endpoint = 'shop.json', $accessToken = '' ){
        if( ! $shopDomain || ! $accessToken ){
            return null;
        }

        try {
            $url = $this->getAdminEndpoint( $shopDomain, $endpoint );
            $headers = [
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token' => $accessToken
            ];

            $client = new Client();
            $response = $client->get( $url, [
                'headers' => $headers
            ]);

            $shopDetails = json_decode( $response->getBody(), true );

            if ( !empty( $shopDetails ) && $response->getStatusCode() == 200 ) {
                return $shopDetails['shop'] ?? null ;
            }

            return false;

        } catch ( GuzzleException $exception ) {
            Log::info( $exception->getMessage() . " " . $exception->getLine() );
            return null;
        }
    }

}
