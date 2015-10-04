<?php

namespace Ivyhjk\Oauth2\Phalcon\Server\Storage\Mongo;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\ScopeInterface;

use Ivyhjk\Oauth2\Phalcon\Server\Storage\BaseStorage;

class Scope extends BaseStorage implements ScopeInterface {

    /**
     * Limit clients to scopes.
     *
     * @var boolean
     **/
    private $limitClientsToScopes = false;

    /**
     * Limit scopes to grants.
     *
     * @var boolean
     **/
    private $limitScopesToGrants = false;

    /**
     * Create a new Scope instance.
     *
     * @param object $database A database connection instance
     * @param boolean $limitClientsToScopes
     * @param boolean $limitScopesToGrants
     *
     * @return void
     **/
    public function __construct($database, $limitClientsToScopes = false, $limitScopesToGrants = false)
    {
        parent::__construct($database);

        $this->limitScopesToGrants = $limitScopesToGrants;
        $this->limitClientsToScopes = $limitClientsToScopes;
    }

    /**
     * Return information about a scope
     *
     * @param string $scope The scope
     * @param string $grantType The grant type used in the request (default = null)
     * @param string $clientId The client sending the request (default = null)
     *
     * @return mixed [\League\OAuth2\Server\Entity\ScopeEntity | null]
     **/
    public function get($scope, $grantType = null, $clientId = null)
    {
        $fields = [
            '_id',
            'name'
        ];

        $conditions = [
            'name' => $scope
        ];

        // Check if scope has this grant.
        if ($this->limitScopesToGrants === true && $grantType !== null) {
            // Get grant
            $grantFields = [
                '_id'
            ];

            $grantConditions = [
                'name' => $grantType
            ];

            $grant = $this->getDatabase()
                ->selectCollection('oauth_grants')
                ->findOne($grantConditions, $grantFields);

            if ($grant === null) {
                return null;
            }

            $conditions = array_merge($conditions, [
                'grants' => $grant['_id']
            ]);
        }

        // Check if exists scope
        $scopeData = $this->getDatabase()
            ->selectCollection('oauth_scopes')
            ->findOne($conditions, $fields);

        if ($scopeData === null) {
            return null;
        }

        // Check if client has this scope.
        if ($this->limitClientsToScopes === true && $clientId !== null) {
            $client = $this->getDatabase()
                ->selectCollection('oauth_clients')
                ->count([
                    '_id' => new \MongoId($clientId),
                    'scopes' => $scopeData['_id']
                ]);

            if ($client <= 0) {
                return null;
            }
        }

        $scope = new ScopeEntity($this->server);

        $scope->hydrate([
            'id' => $scopeData['_id']->__toString(),
            'description' => $scopeData['name'],
        ]);

        return $scope;
    }
}
