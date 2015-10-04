<?php

namespace Ivyhjk\Oauth2\Phalcon\Server\Storage\Mongo;

use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Exception\InvalidGrantException;

use Ivyhjk\Oauth2\Phalcon\Server\Storage\BaseStorage;

class Client extends BaseStorage implements ClientInterface {

    /**
     * Limit clients to grants.
     *
     * @var bool
     **/
    private $limitClientsToGrant = false;

    /**
     * Class constructor.
     *
     * @param object $database A database instance for queries.
     * @param boolean $limitClientsToGrant Limit clients to grant access.
     *
     * @return void
     **/
    public function __construct($database, $limitClientsToGrant = false)
    {
        parent::__construct($database);

        $this->limitClientsToGrant = $limitClientsToGrant;
    }

    /**
     * Validate a client
     *
     * @param string $clientId The client's ID
     * @param string $clientSecret The client's secret (default = null)
     * @param string $redirectUri The client's redirect URI (default = null)
     * @param string $grantType The grant type used (default = null)
     *
     * @return mixed [\League\OAuth2\Server\Entity\ClientEntity | null]
     *
     * @throws \League\OAuth2\Server\Exception\InvalidGrantException When send an invalid grant type
     **/
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        if ($clientId === null && ($clientSecret === null || $redirectUri === null)) {
            return null;
        }

        $fields = [
            '_id',
            'name',
            'secret',
            'grants',
        ];

        $conditions = [
            '_id' => new \MongoId($clientId)
        ];

        if ($redirectUri !== null) {
            $conditions = array_merge($conditions, [
                'redirect_uri' => $redirectUri
            ]);
        }

        if ($clientSecret !== null) {
            $conditions = array_merge($conditions, [
                'secret' => $clientSecret
            ]);
        }

        if ($this->limitClientsToGrant === true && $grantType !== null) {
            $grantConditions = [
                'name' => $grantType
            ];

            $grantFields = [
                '_id'
            ];

            $grant = $this->getDatabase()
                ->selectCollection('oauth_grants')
                ->findOne($grantConditions, $grantFields);

            if ($grant === null) {
                throw new InvalidGrantException('grant_type');
            }

            $grantId = $grant['_id'];

            $conditions = array_merge($conditions, [
                'grants' => $grantId
            ]);
        }

        $result = $this->getDatabase()
            ->selectCollection('oauth_clients')
            ->findOne($conditions, $fields);

        if ($result === null) {
            return null;
        }

        if ($redirectUri !== null && ! empty($result['redirect_uri'])) {
            $redirect_uri = $redirectUri;
        } else {
            $redirect_uri = null;
        }

        if ($clientSecret !== null && $result['secret']) {
            $client_secret = $result['secret'];
        } else {
            $client_secret = null;
        }

        $client = new ClientEntity($this->server);

        $client->hydrate([
            'name' => $result['name'],
            'secret' => $client_secret,
            'redirectUri' => $redirect_uri,
            'id' => $result['_id']->__toString(),
        ]);


        return $client;
    }

    /**
     * Get the client associated with a session
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
     *
     * @return mixed [\League\OAuth2\Server\Entity\ClientEntity | null]
     **/
    public function getBySession(SessionEntity $session)
    {
        $session = $this->getDatabase()
            ->selectCollection('oauth_sessions')
            ->findOne([
                '_id' => new \MongoId($session->getId())
            ], [
                'client_id'
            ]);

        if ($session === null) {
            return null;
        }

        $client = $this->getDatabase()
            ->selectCollection('oauth_clients')
            ->findOne([
                '_id' => new \MongoId($session['client_id'])
            ], [
                '_id',
                'secret',
                'name',
            ]);

        if ($client === null) {
            return null;
        }

        $entity = new ClientEntity($this->server);

        $entity->hydrate([
            'name' => $client['name'],
            'secret' => $client['secret'],
            'id' => $session['client_id']
        ]);

        return $entity;
    }
}
