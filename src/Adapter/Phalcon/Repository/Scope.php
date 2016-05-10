<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Repository;

use Phalcon\Mvc\Model\Query\Builder;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

use Ivyhjk\OAuth2\Server\Entity\Scope as ScopeEntity;

class Scope extends \Ivyhjk\OAuth2\Server\Repository\Base implements ScopeRepositoryInterface
{
    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\ScopeEntityInterface
     **/
    public function getScopeEntityByIdentifier($identifier)
    {
        $result = (new Builder())
            ->columns([
                'Scope.id'
            ])
            ->addFrom(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Scope::class, 'Scope')
            ->where('Scope.id = :identifier:', compact('identifier'))
            ->limit(1)
            ->getQuery()
            ->getSingleResult();

        if ( ! $result) {
            throw OAuthServerException::invalidScope($identifier);
        }

        $scope = new ScopeEntity();
        $scope->setIdentifier($result->id);

        return $scope;
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param array<ScopeEntityInterface> $scopes
     * @param string $grantType
     * @param \League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface $clientEntity
     * @param string $userIdentifier
     *
     * @return array<\League\OAuth2\Server\Entities\Interfaces\ScopeEntityInterface>
     **/
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        $builder = (new Builder())
            ->columns([
                'Scope.id'
            ])
            ->addFrom(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Scope::class, 'Scope');

        $scopesIdentifiers = [];

        foreach ($scopes as $scope) {
            $scopesIdentifiers[] = $scope->getIdentifier();
        }

        $builder->inWhere('Scope.id', $scopesIdentifiers);

        if ($this->getConfig()->limit_scopes_to_grants === true) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\GrantScope::class, 'GrantScope.scope_id = Scope.id', 'GrantScope')
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Grant::class, 'Grant.id = GrantScope.grant_id', 'Grant')
                ->andWhere('Grant.id = :grantType:', compact('grantType'));
        }

        if ($this->getConfig()->limit_clients_to_scopes === true) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\ClientScope::class, 'ClientScope.scope_id = Scope.id', 'ClientScope')
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Client::class, 'Client.id = ClientScope.client_id', 'Client')
                ->andWhere('Client.id = :client_id:', [
                    'client_id' => $clientEntity->getIdentifier()
                ]);
        }

        if ($this->getConfig()->limit_users_to_scopes === true) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\UserScope::class, 'UserScope.scope_id = Scope.id', 'UserScope')
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\User::class, 'User.id = UserScope.user_id', 'User')
                ->AndWhere('User.id = :userIdentifier:', compact('userIdentifier'));
        }

        $query = $builder->getQuery();
        $result = $query->execute();

        if ( ! $result || $result->count() <= 0) {
            $scope = current($scopes);

            throw OAuthServerException::invalidScope($scope->getIdentifier());
        }

        $entities = [];

        foreach ($result as $scope) {
            $entity = new ScopeEntity();
            $entity->setIdentifier($scope->id);

            $entities[] = $entity;
        }

        return $entities;
    }
}
