<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Repository;

use Phalcon\Security;
use Phalcon\Mvc\Model\Query\Builder;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

use Ivyhjk\OAuth2\Server\Entity\User as UserEntity;

class User extends \Ivyhjk\OAuth2\Server\Repository\Base implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $builder = (new Builder())
            ->columns([
                'User.id',
                'User.username',
                'User.password'
            ])
            ->addFrom(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\User::class, 'User')
            ->where('User.username = :username:', compact('username'))
            ->limit(1);

        if ($this->getConfig()->limit_users_to_clients === true) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\UserClient::class, 'UserClient.user_id = User.id', 'UserClient')
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Client::class, 'Client.id = UserClient.client_id', 'Client')
                ->andWhere('Client.id = :client_id:', [
                    'client_id' => $clientEntity->getIdentifier()
                ]);
        }

        if ($this->getConfig()->limit_users_to_grants === true) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\UserGrant::class, 'UserGrant.user_id = User.id', 'UserGrant')
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Grant::class, 'Grant.id = UserGrant.grant_id', 'Grant')
                ->andWhere('Grant.id = :grantType:', compact('grantType'));
        }

        $query = $builder->getQuery();
        $result = $query->getSingleResult();

        if ( ! $result) {
            throw OAuthServerException::invalidCredentials();
        }

        $security = new Security();

        if ($security->checkHash($password, $result->password) !== true) {
            throw OAuthServerException::invalidCredentials();
        }

        $user = new UserEntity();
        $user->setIdentifier($result->id);

        return $user;
    }
}
