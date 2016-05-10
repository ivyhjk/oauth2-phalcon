<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Repository;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

use Ivyhjk\OAuth2\Server\Entity\RefreshToken as RefreshTokenEntity;

class RefreshToken extends \Ivyhjk\OAuth2\Server\Repository\Base implements RefreshTokenRepositoryInterface
{
    /**
     * Creates a new refresh token
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\RefreshTokenEntityInterface
     **/
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * Create a new refresh token_name.
     *
     * @param \League\OAuth2\Server\Entities\Interfaces\RefreshTokenEntityInterface $refreshTokenEntity
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $refreshToken = new \Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\RefreshToken();
        $refreshToken->id = $refreshTokenEntity->getIdentifier();
        $refreshToken->access_token_id = $refreshTokenEntity->getAccessToken()->getIdentifier();
        $refreshToken->expire_time = $refreshTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s');

        if ( ! $refreshToken->save()) {
            prx($refreshToken->getMessages());
        }
    }

    /**
     * Revoke the refresh token.
     *
     * @param String $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        $refreshToken = \Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\RefreshToken::findFirst([
            'conditions' => 'id = :tokenId:',
            'bind' => compact('tokenId')
        ]);

        $refreshToken->revoked = 1;

        if ( ! $refreshToken->update()) {
            prx($refreshToken->getMessages());
        }
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param String $tokenId
     *
     * @return Bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $revoked = \Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\RefreshToken::count([
            'conditions' => 'id = :tokenId: AND (revoked = 1 OR expire_time <= "' . date('Y-m-d H:i:s') . '")',
            'bind' => compact('tokenId')
        ]);

        return $revoked > 0;
    }
}
