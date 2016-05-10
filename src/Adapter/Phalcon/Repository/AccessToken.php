<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Repository;

use Phalcon\Mvc\Model\Query\Builder;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Ivyhjk\OAuth2\Server\Entity\AccessToken as AccessTokenEntity;

class AccessToken extends \Ivyhjk\OAuth2\Server\Repository\Base implements AccessTokenRepositoryInterface
{
    /**
     * Create a new access token
     *
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     * @param mixed $userIdentifier
     *
     * @return AccessTokenEntityInterface
     **/
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);

        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param \League\OAuth2\Server\Entities\Interfaces\AccessTokenEntityInterface $accessTokenEntity
     **/
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $scopes = [];

        foreach ($accessTokenEntity->getScopes() as $scope) {
            $scopes[] = \Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Scope::findFirst([
                'conditions' => 'id = :id:',
                'bind' => [
                    'id' => $scope->getIdentifier()
                ]
            ]);
        }

        // prx($accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'));

        $accessToken = new \Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\AccessToken();
        $accessToken->id = $accessTokenEntity->getIdentifier();
        $accessToken->user_id = $accessTokenEntity->getUserIdentifier();
        $accessToken->client_id = $accessTokenEntity->getClient()->getIdentifier();
        $accessToken->expire_time = $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s');

        $accessToken->scopes = $scopes;

        $accessToken->save();
    }

    /**
     * Revoke an access token.
     *
     * @param String $tokenId
     **/
    public function revokeAccessToken($tokenId)
    {
        $token = \Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\AccessToken::findFirst([
            'conditions' => 'id = :tokenId:',
            'bind' => compact('tokenId')
        ]);

        $token->revoked = 1;

        if ( ! $token->update()) {
            prx($token->getMessages());
        }
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param String $tokenId
     *
     * @return Bool Return true if this token has been revoked
     **/
    public function isAccessTokenRevoked($tokenId)
    {
        $revoked = \Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\AccessToken::count([
            'conditions' => 'id = :tokenId: AND (revoked = 1 OR expire_time <= "' . date('Y-m-d H:i:s') . '")',
            'bind' => compact('tokenId')
        ]);

        return $revoked > 0;
    }
}
