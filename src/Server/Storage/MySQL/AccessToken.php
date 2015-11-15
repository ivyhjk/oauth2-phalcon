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
     * @return [\League\OAuth2\Server\Entity\AccessTokenEntity|null]
     */
    public function get($token)
    {
        $query = $this->getDatabase()
            ->prepare('SELECT * FROM oauth_access_tokens AS Token WHERE Token.id = :token_id');

        $query->execute([
            ':token_id' => $token
        ]);

        $result = $query->fetch();

        dd('D:');

        if ($result === false) {
            return null;
        }

        dd($result);

        dd(get_class_methods($this->getDatabase()));
        var_dump(get_class_methods($test));

        $result = $test->fetch();

        dd($result);
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
        $accessToken->sessionId = $sessionId;
        $accessToken->created_at = date('Y-m-d H:i:s');
        $accessToken->save();

        $entity = new AccessTokenEntity($this->server);

        $entity
            ->setId($token)
            ->setExpireTime((int) $expireTime);

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
