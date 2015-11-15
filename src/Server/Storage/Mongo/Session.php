<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Storage\Mongo;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Storage\SessionInterface;

use Ivyhjk\OAuth2\Phalcon\Server\Storage\BaseStorage;

class Session extends BaseStorage implements SessionInterface {
    /**
     * Get a session from an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $accessToken The access token
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity | null
     **/
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        dd(1);
    }

    /**
     * Get a session from an auth code
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $authCode The auth code
     *
     * @return mixed [\League\OAuth2\Server\Entity\SessionEntity | null]
     **/
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        dd(2);
    }

    /**
     * Get a session's scopes
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(SessionEntity $session)
    {
        $session_id = $session->getId();

        if ($session_id === null) {
            return [];
        }

        dd(compact('session_id', 'session'));
        dd(3);

         // TODO: Check this before pushing
        $result = $this->getConnection()->table('oauth_session_scopes')
                  ->select('oauth_scopes.*')
                  ->join('oauth_scopes', 'oauth_session_scopes.scope_id', '=', 'oauth_scopes.id')
                  ->where('oauth_session_scopes.session_id', $session->getId())
                  ->get();

        $scopes = [];

        foreach ($result as $scope) {
            $scopes[] = (new ScopeEntity($this->getServer()))->hydrate([
                'id' => $scope->id,
                'description' => $scope->description,
            ]);
        }
        return $scopes;
    }

    /**
     * Create a new session
     *
     * @param string $ownerType Session owner's type (user, client)
     * @param string $ownerId Session owner's ID
     * @param string $clientId Client ID
     * @param string $clientRedirectUri Client redirect URI (default = null)
     *
     * @return integer The session's ID
     **/
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
        $session = [
            'client_id' => $clientId,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'client_redirect_uri' => $clientRedirectUri,
            'created_at' => new \MongoDate()
        ];

        $this->getDatabase()
            ->selectCollection('oauth_sessions')
            ->insert($session);

        return $session['_id']->__toString();
    }

    /**
     * Associate a scope with a session
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
     * @param \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     *
     * @return void
     **/
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $updated = $this->getDatabase()
            ->selectCollection('oauth_sessions')
            ->update([
                '_id' => new \MongoId($session->getId())
            ], [
                '$push' => [
                    'scopes' => $scope->getId()
                ]
            ]);
    }
}
