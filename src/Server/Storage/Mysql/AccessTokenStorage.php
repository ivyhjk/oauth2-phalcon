<?php

namespace Ivyhjk\Oauth2\Phalcon\Server\Storage;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Storage\AccessTokenInterface;

class AccessTokenStorage extends BaseStorage implements AccessTokenInterface {
	/**
     * Get an instance of Entity\AccessTokenEntity
     *
     * @param string $token The access token
     *
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity | null
     */
    public function get($token)
    {
        dd(1);
    }

    /**
     * Get the scopes for an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AccessTokenEntity $token)
    {
        dd(2);
    }

    /**
     * Creates a new access token
     *
     * @param string         $token      The access token
     * @param integer        $expireTime The expire time expressed as a unix timestamp
     * @param string|integer $sessionId  The session ID
     *
     * @return void
     */
    public function create($token, $expireTime, $sessionId)
    {
        dd(3);
    }

    /**
     * Associate a scope with an acess token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     * @param \League\OAuth2\Server\Entity\ScopeEntity       $scope The scope
     *
     * @return void
     */
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
    {
        dd(4);
    }

    /**
     * Delete an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token to delete
     *
     * @return void
     */
    public function delete(AccessTokenEntity $token)
    {
        dd(5);
    }
}
