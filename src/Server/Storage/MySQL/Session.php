<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Storage\MySQL;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Storage\SessionInterface;

use Ivyhjk\OAuth2\Phalcon\Server\Storage\BaseStorage;

class Session extends BaseStorage implements SessionInterface {
    /**
     * Get a session from an access token
     *session_id
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $accessToken The access token
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity | null
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        dd(1);
    }

    /**
     * Get a session from an auth code
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $authCode The auth code
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity | null
     */
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        dd(2);
    }

    /**
     * Get a session's scopes
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     **/
    public function getScopes(SessionEntity $session)
    {
        $builder = $this->getDatabase()
            ->createBuilder()
            ->columns([
                'Scope.id',
                'Scope.description',
            ])
            ->addFrom(\Ivyhjk\OAuth2\Phalcon\Models\SessionScope::class, 'SessionScope')
            ->join(\Ivyhjk\OAuth2\Phalcon\Models\Scope::class, 'Scope.id = SessionScope.scope_id', 'Scope')
            ->where('SessionScope.session_id = :session_id:', [
                'session_id' => $session->getId()
            ]);

        $query = $builder->getQuery();

        $result = $query->execute();

        $scopes = [];

        foreach ($result as $scope) {
            dd($scope);
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
        $session = new \Ivyhjk\OAuth2\Phalcon\Models\Session();
        $session->client_id = $clientId;
        $session->owner_type = $ownerType;
        $session->owner_id = $ownerId;
        $session->client_redirect_uri = $clientRedirectUri;
        $session->created_at = date('Y-m-d H:i:s');
        $session->save();

        return $session->id;
    }

    /**
     * Associate a scope with a session
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
     * @param \League\OAuth2\Server\Entity\ScopeEntity   $scope   The scope
     *
     * @return void
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {

        $sessionScope = new \Ivyhjk\OAuth2\Phalcon\Models\SessionScope();
        $sessionScope->session_id = $session->getId();
        $sessionScope->scope_id = $scope->getId();
        $sessionScope->created_at = date('Y-m-d H:i:s');

        $sessionScope->save();
    }
}
