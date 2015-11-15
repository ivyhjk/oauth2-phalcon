<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Storage\MySQL;

use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;

use Ivyhjk\OAuth2\Phalcon\Server\Storage\BaseStorage;

class Client extends BaseStorage implements ClientInterface {
    /**
     * Limit clients to grants.
     *
     * @var bool
     **/
    private $limitClientsToGrants = false;

    /**
     * Class constructor.
     *
     * @param object $database A database instance for queries.
     * @param boolean $limitClientsToGrants Limit clients to grant access.
     *
     * @return void
     **/
    public function __construct($database, $limitClientsToGrants = false)
    {
        parent::__construct($database);

        $this->limitClientsToGrants = $limitClientsToGrants;
    }

    /**
     * Validate a client
     *
     * @param string $clientId The client's ID
     * @param string $clientSecret The client's secret (default = "null")
     * @param string $redirectUri The client's redirect URI (default = "null")
     * @param string $grantType The grant type used (default = "null")
     *
     * @return \League\OAuth2\Server\Entity\ClientEntity | null
     **/
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        // dd(compact('clientId', 'clientSecret', 'redirectUri', 'grantType'));

        // dd(class_exists('\\Ivyhjk\\OAuth2\\Phalcon\\Models\\Client'));
        $builder = $this->getDatabase()->createBuilder();

        $builder
            ->addFrom('\\Ivyhjk\\OAuth2\\Phalcon\\Models\\Client', 'Client');

        if ($redirectUri !== null && $clientSecret === null) {
            dd(1);
        } else if ($clientSecret !== null && $redirectUri === null) {
            $builder
                ->columns([
                    'Client.id',
                    'Client.secret',
                    'Client.name',
                ])
                ->where('Client.id = :clientId:', compact('clientId'))
                ->andWhere('Client.secret = :clientSecret:', compact('clientSecret'));
        } else if ($clientSecret !== null && $redirectUri !== null) {
            dd(2);
        }

        if ($this->limitClientsToGrants === true && $grantType !== null) {
            $builder
                ->innerJoin('\\Ivyhjk\\OAuth2\\Phalcon\\Models\\ClientGrant', 'ClientGrant.client_id = Client.id', 'ClientGrant')
                ->innerJoin('\\Ivyhjk\\OAuth2\\Phalcon\\Models\\Grant', 'Grant.id = ClientGrant.grant_id', 'Grant')
                ->andWhere('Grant.id = :grantType:', compact('grantType'));
        }


        $query = $builder->getQuery();
        // dd(get_class_methods($query));
        $result = $query
            ->getSingleResult();

        if ($result === false) {
            return null;
        }

        // dd($result);

        $client = new ClientEntity($this->server);

        $client->hydrate([
            'name' => $result->name,
            'secret' => $result->secret,
            'redirectUri' => isset($result->redirect_uri) ? $result->redirect_uri : null,
            'id' => $result->id,
        ]);

        return $client;
    }

    /**
     * Get the client associated with a session
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
     *
     * @return \League\OAuth2\Server\Entity\ClientEntity | null
     */
    public function getBySession(SessionEntity $session)
    {
        dd(2);
    }
}
