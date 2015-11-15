<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Storage\Mongo;

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
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity | null
     **/
    public function get($token)
    {
        $fields = [
            '_id',
            'expire_time',
        ];

        $conditions = [
            '_id' => $token
        ];

        $result = $this->getDatabase()
            ->selectCollection('oauth_access_tokens')
            ->findOne($conditions, $fields);

        // dd(compact('result', 'conditions'));

        if ($result === null) {
            return null;
        }

        $entity = new AccessTokenEntity($this->server);

        $entity
            ->setId($result['_id'])
            ->setExpireTime($result['expire_time']);

        return $entity;
    }

    /**
     * Get the scopes for an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     **/
    public function getScopes(AccessTokenEntity $token)
    {
        $token_id = $token->getId();

        if ($token_id === null) {
            return [];
        }

        $fields = [
            'scopes'
        ];

        $conditions = [
            '_id' => $token_id,
        ];

        $result = $this->getDatabase()
            ->selectCollection('oauth_access_tokens')
            ->findOne($conditions, $fields);

        if ( ! isset($result['scopes']) || empty($result['scopes'])) {
            return [];
        }

        dd(compact('token_id', 'result'));

        $result = $this->getConnection()->table('oauth_access_token_scopes')
                ->select('oauth_scopes.*')
                ->join('oauth_scopes', 'oauth_access_token_scopes.scope_id', '=', 'oauth_scopes.id')
                ->where('oauth_access_token_scopes.access_token_id', $token->getId())
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
     * Creates a new access token
     *
     * @param string $token The access token
     * @param integer expireTime The expire time expressed as a unix timestamp
     * @param mixed [string|integer] $sessionId The session ID
     *
     * @return void
     **/
    public function create($token, $expireTime, $sessionId)
    {
        $accessToken = [
            '_id' => $token,
            'session_id' => $sessionId,
            'expire_time' => $expireTime,
            'created_at' => new \MongoDate()
        ];

        $this->getDatabase()
            ->selectCollection('oauth_access_tokens')
            ->insert($accessToken);

        $entity = new AccessTokenEntity($this->server);

        $entity->setId($accessToken['_id'])
                ->setExpireTime((int) $expireTime);


        $this->delete($entity);

        return $entity;
    }

    /**
     * Associate a scope with an acess token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     * @param \League\OAuth2\Server\Entity\ScopeEntity       $scope The scope
     *
     * @return void
     **/
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
    {
        $token_id = $token->getId();
        $scope_id = $scope->getId();

        $this->getDatabase()
            ->selectCollection('oauth_access_tokens')
            ->update([
                '_id' => $token_id
            ], [
                '$push' => [
                    'scopes' => $scope_id
                ]
            ]);
    }

    /**
     * Delete an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token to delete
     *
     * @return void
     **/
    public function delete(AccessTokenEntity $token)
    {
        $deleted = $this->getDatabase()
            ->selectCollection('oauth_access_tokens')
            ->remove([
                '_id' => $token->getId()
            ]);
    }
}
