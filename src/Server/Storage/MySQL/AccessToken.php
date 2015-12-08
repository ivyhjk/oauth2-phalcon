<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Storage\MySQL;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Storage\AccessTokenInterface;

use Ivyhjk\OAuth2\Phalcon\Server\Storage\BaseStorage;

class AccessToken extends BaseStorage implements AccessTokenInterface {
    /**
     * Get an instance of Entity\AccessTokenEntity
     *
     * @param string $token The access token
     *
     * @return mixed [\League\OAuth2\Server\Entity\AccessTokenEntity|null]
     **/
    public function get($token)
    {
        $result = $this->getDatabase()->createBuilder()
            ->columns([
                'AccessToken.id',
                'AccessToken.expire_time',
            ])
            ->addFrom(\Ivyhjk\OAuth2\Phalcon\Models\AccessToken::class, 'AccessToken')
            ->where('AccessToken.id = :token:', compact('token'))
            ->getQuery()
            ->getSingleResult();

        if ($result === false) {
            return null;
        }

        $entity = new AccessTokenEntity($this->server);

        return $entity
            ->setId($result->id)
            ->setExpireTime((int) $result->expire_time);
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
        $builder = $this->getDatabase()->createBuilder()
            ->addFrom(\Ivyhjk\OAuth2\Phalcon\Models\AccessTokenScope::class, 'AccessTokenScope')
            ->join(\Ivyhjk\OAuth2\Phalcon\Models\Scope::class, 'Scope.id = AccessTokenScope.scope_id', 'Scope')
            ->where('AccessTokenScope.access_token_id = :token_id:', [
                'token_id' => $token->getId()
            ]);

        $query = $builder->getQuery();

        $result = $query->execute();

        $scopes = [];

        foreach ($result as $scope) {
            dd($scope);
        }

        return $scopes;

        // $result = $this->getConnection()->table('oauth_access_token_scopes')
        //         ->select('oauth_scopes.*')
        //         ->join('oauth_scopes', 'oauth_access_token_scopes.scope_id', '=', 'oauth_scopes.id')
        //         ->where('oauth_access_token_scopes.access_token_id', $token->getId())
        //         ->get();
        // $scopes = [];
        // foreach ($result as $scope) {
        //     $scopes[] = (new ScopeEntity($this->getServer()))->hydrate([
        //        'id' => $scope->id,
        //         'description' => $scope->description,
        //     ]);
        // }
        // return $scopes;
    }

    /**
     * Creates a new access token
     *
     * @param string $token The access token
     * @param integer $expireTime The expire time expressed as a unix timestamp
     * @param mixed [string|integer] $sessionId The session ID
     *
     * @return void
     **/
    public function create($token, $expireTime, $sessionId)
    {
        $accessToken = new \Ivyhjk\OAuth2\Phalcon\Models\AccessToken();
        $accessToken->id = $token;
        $accessToken->expire_time = $expireTime;
        $accessToken->session_id = $sessionId;
        $accessToken->created_at = date('Y-m-d H:i:s');

        try {
            $saved = $accessToken->save();
        } catch (\Exception $e) {
            // dd($e);
        }

        $entity = new AccessTokenEntity($this->server);

        $entity
            ->setId($token)
            ->setExpireTime($expireTime);

        return $entity;
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
        $accessTokenScope = new \Ivyhjk\OAuth2\Phalcon\Models\AccessTokenScope();
        $accessTokenScope->access_token_id = $token->getId();
        $accessTokenScope->scope_id = $scope->getId();
        $accessTokenScope->created_at = date('Y-m-d H:i:s');

        $accessTokenScope->save();
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
