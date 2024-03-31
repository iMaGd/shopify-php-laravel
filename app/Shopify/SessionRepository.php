<?php

namespace App\Shopify;

use App\Models\ShopifySession;
use Exception;
use Illuminate\Support\Facades\Log;
use Shopify\Auth\AccessTokenOnlineUserInfo;
use Shopify\Auth\Session;
use Shopify\Auth\SessionStorage;
use Shopify\Auth\OAuth as ShopifyOAuth;

class SessionRepository implements SessionStorage
{

    public function storeSession( Session $session ): bool
    {
        if ( ! $dbSession = ShopifySession::where( 'session_id', $session->getId())->first() ) {
            $dbSession = new ShopifySession();
        }

        $dbSession->session_id = $session->getId();
        $dbSession->shop = $session->getShop();
        $dbSession->myshopify_domain = $session->getShop();
        $dbSession->state = $session->getState();
        $dbSession->is_online = $session->isOnline();
        $dbSession->access_token = $session->getAccessToken();
        $dbSession->expires_at = $session->getExpires();
        $dbSession->scope = $session->getScope();

        if ( ! empty( $session->getOnlineAccessInfo() ) ) {
            $dbSession->user_id = $session->getOnlineAccessInfo()->getId();
            $dbSession->user_first_name = $session->getOnlineAccessInfo()->getFirstName();
            $dbSession->user_last_name = $session->getOnlineAccessInfo()->getLastName();
            $dbSession->user_email = $session->getOnlineAccessInfo()->getEmail();
            $dbSession->user_email_verified = $session->getOnlineAccessInfo()->isEmailVerified();
            $dbSession->account_owner = $session->getOnlineAccessInfo()->isAccountOwner();
            $dbSession->locale = $session->getOnlineAccessInfo()->getLocale();
            $dbSession->collaborator = $session->getOnlineAccessInfo()->isCollaborator();
        }

        try {
            return $dbSession->save();
        } catch ( Exception $err) {
            Log::error("Failed to save session to database: " . $err->getMessage());
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function loadSession( string $sessionId ): ?Session
    {
        $dbSession = ShopifySession::where( 'session_id', $sessionId )->first();

        if ( $dbSession ) {
            $session = new Session(
                $dbSession->session_id,
                $dbSession->shop,
                $dbSession->is_online == 1,
                $dbSession->state
            );
            if ($dbSession->expires_at) {
                $session->setExpires($dbSession->expires_at);
            }
            if ($dbSession->access_token) {
                $session->setAccessToken($dbSession->access_token);
            }
            if ($dbSession->scope) {
                $session->setScope($dbSession->scope);
            }
            if ($dbSession->user_id) {
                $onlineAccessInfo = new AccessTokenOnlineUserInfo(
                    (int)$dbSession->user_id,
                    $dbSession->user_first_name,
                    $dbSession->user_last_name,
                    $dbSession->user_email,
                    $dbSession->user_email_verified == 1,
                    $dbSession->account_owner == 1,
                    $dbSession->locale,
                    $dbSession->collaborator == 1
                );
                $session->setOnlineAccessInfo( $onlineAccessInfo );
            }
            return $session;
        }
        return null;
    }

    public function deleteSession( string $sessionId ): bool
    {
        return ShopifySession::where('session_id', $sessionId)->delete() === 1;
    }

    /**
     * @throws Exception
     */
    public function loadSessionByShopDomain( string $shopDomain ): Session
    {
        $sessionId = ShopifyOAuth::getOfflineSessionId( $shopDomain );
        return $this->loadSession( $sessionId );
    }

    public function getByShopDomain( $shop ) {
        return ShopifySession::where( 'shop', $shop )->first();
    }

    public function getUserIdByShopDomain( $shop ){
        return $this->getByShopDomain( $shop )?->user_id;
    }

    public function storeShopInfo( $sessionId, $shopInfo ){
        $dbSession = ShopifySession::where('session_id', $sessionId )->first();

        if ( ! $dbSession || ! empty( $dbSession->user_id ) ) {
            return null;
        }

        $fullName = trim( $shopInfo['shop_owner'] );
        $nameDetails = $this->extractFullName( $fullName );

        $dbSession->shop_id = $shopInfo['id'];
        $dbSession->user_id = $shopInfo['user_id'];
        $dbSession->user_full_name = $shopInfo['shop_owner'];
        $dbSession->user_first_name = $nameDetails['firstName'];
        $dbSession->user_last_name = $nameDetails['lastName'];
        $dbSession->user_email = $shopInfo['email'];
        $dbSession->user_country_code = $shopInfo['country_code'];
        $dbSession->user_province = $shopInfo['province'];
        $dbSession->myshopify_domain = $shopInfo['myshopify_domain'];
        $dbSession->locale = $shopInfo['primary_locale'];
        $dbSession->user_email_verified = 1;

        $dbSession->save();
    }

    /**
     *
     * @param $shopDomain
     *
     * @return ShopifySession
     */
    public function getDatabaseSessionByShop( $shopDomain ){
        $sessionId = ShopifyOAuth::getOfflineSessionId( $shopDomain );
        return ShopifySession::where('session_id', $sessionId)?->first();
    }

    private function extractFullName( $fullName ) {
        $firstSpacePosition = strpos( $fullName, ' ');
        $lastSpacePosition = strrpos( $fullName, ' ');

        if ($firstSpacePosition === false) {
            return [
                'firstName' => $fullName,
                'lastName' => '',
            ];
        } elseif ($firstSpacePosition === $lastSpacePosition) {
            return [
                'firstName' => substr( $fullName, 0, $firstSpacePosition),
                'lastName' => substr( $fullName, $lastSpacePosition + 1),
            ];
        } else {
            return [
                'firstName' => substr( $fullName, 0, $firstSpacePosition),
                'lastName' => substr( $fullName, $lastSpacePosition + 1),
            ];
        }
    }
}
