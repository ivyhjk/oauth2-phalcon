<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Repository;

use Phalcon\Mvc\Model\Query\Builder;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

use Ivyhjk\OAuth2\Server\Entity\Client as ClientEntity;

class Client extends \Ivyhjk\OAuth2\Server\Repository\Base implements ClientRepositoryInterface
{
    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     * @param string $grantType The grant type used
     * @param string $clientSecret The client's secret (if sent)
     *
     * @return \League\OAuth2\Server\Entities\Interfaces\ClientEntityInterface
     **/
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $builder = (new Builder())
            ->columns([
                'Client.id',
                'Client.secret',
                'Client.name'
            ])
            ->addFrom(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Client::class, 'Client')
            ->where('Client.id = :clientIdentifier:', compact('clientIdentifier'))
            ->limit(1);

        if ($mustValidateSecret === true) {
            $builder->andWhere('Client.secret = :clientSecret:', compact('clientSecret'));
        }

        //
        if ($this->getConfig()->limit_clients_to_grants === true) {
            $builder
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\ClientGrant::class, 'ClientGrant.client_id = Client.id', 'ClientGrant')
                ->innerJoin(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Grant::class, 'Grant.id = ClientGrant.grant_id', 'Grant')
                ->andWhere('Grant.id = :grantType:', compact('grantType'));
        }

        $query = $builder->getQuery();
        $result = $query->getSingleResult();

        if ( ! $result) {
            throw OAuthServerException::invalidClient();
        }

        // Get one endpoint?
        // $builder = $this->getDatabase()->createBuilder();
        //
        // $builder
        //     ->columns([
        //         'ClientEndpoint.redirect_uri'
        //     ])
        //     ->addFrom(\Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\ClientEndpoint::class, 'ClientEndpoint')
        //     ->where('ClientEndpoint.client_id = :client_id:', [
        //         'client_id' => $result->id
        //     ])
        //     ->limit(1);
        //
        // $endpoint = $builder->getQuery()->getSingleResult();

        $client = new ClientEntity();
        $client->setName($result->name);
        $client->setIdentifier($result->id);

        // if ($endpoint) {
        //     $client->setRedirectUri($endpoint->redirect_uri);
        // }

        return $client;
    }
}
