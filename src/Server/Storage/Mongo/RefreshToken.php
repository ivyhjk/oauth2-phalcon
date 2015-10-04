<?php

namespace Ivyhjk\Oauth2\Phalcon\Server\Storage\Mongo;

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\RefreshTokenInterface;

use Ivyhjk\Oauth2\Phalcon\Server\Storage\BaseStorage;

class RefreshToken extends BaseStorage implements RefreshTokenInterface {
    /**
     * Return a new instance of \League\OAuth2\Server\Entity\RefreshTokenEntity
     *
     * @param string $token
     *
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity | null
     **/
    public function get($token)
    {
        dd(1);
    }

    /**
     * Create a new refresh token_name
     *
     * @param string  $token
     * @param integer $expireTime
     * @param string  $accessToken
     *
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     **/
    public function create($token, $expireTime, $accessToken)
    {
        dd(2);
    }

    /**
     * Delete the refresh token
     *
     * @param \League\OAuth2\Server\Entity\RefreshTokenEntity $token
     *
     * @return void
     **/
    public function delete(RefreshTokenEntity $token)
    {
        dd(3);
    }
}
