<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Storage\MySQL;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\ScopeInterface;

use Ivyhjk\OAuth2\Phalcon\Server\Storage\BaseStorage;

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
     * Class constructor.
     *
     * @param object $database A database instance for queries.
     * @param boolean $limitClientsToScopes Limit Clients to Scopes.
     * @param boolean $limitScopesToGrants Limit Scopes to Grants
     *
     * @return void
     **/
    public function __construct($database, $limitClientsToScopes = false, $limitScopesToGrants = false)
    {
        parent::__construct($database);

        $this->limitClientsToScopes = $limitClientsToScopes;
        $this->limitScopesToGrants = $limitScopesToGrants;
    }

    /**
     * Return information about a scope
     *
     * @param string $scope The scope
     * @param string $grantType The grant type used in the request.
     * @param string $clientId The client sending the request.
     *
     * @return mixed [\League\OAuth2\Server\Entity\ScopeEntity|null]
     **/
    public function get($scope, $grantType = null, $clientId = null)
    {
        $builder = $this->getDatabase()->createBuilder()
            ->columns([
                'Scope.id',
                'Scope.description'
            ])
            ->addFrom(\Ivyhjk\OAuth2\Phalcon\Models\Scope::Class, 'Scope')
            ->where('Scope.id = :scope:', compact('scope'))
            ->andWhere('Scope.deleted_at IS NULL');

        if ($this->limitClientsToScopes === true && $clientId !== null) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Phalcon\Models\ClientScope::class, 'ClientScope.scope_id = Scope.id', 'ClientScope')
                ->andWhere('ClientScope.client_id = :clientId:', compact('clientId'));
        }

        if ($this->limitScopesToGrants === true & $grantType !== null) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Phalcon\Models\GrantScope::class, 'GrantScope.scope_id = Scope.id', 'GrantScope')
                ->andWhere('GrantScope.grant_id = :grantType:', compact('grantType'));
        }

        $query = $builder->getQuery();

        $result = $query->getSingleResult();

        if ($result === false) {
            return null;
        }

        $entity = new ScopeEntity($this->server);
        $entity->hydrate([
            'id' => $result->id,
            'description' => $result->description
        ]);

        return $entity;
    }
}
