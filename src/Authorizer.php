<?php

namespace Ivyhjk\OAuth2\Server;

class Authorizer extends \Phalcon\Di\Injectable
{
    /**
     * OAuth2 server configurations.
     *
     * @var \Phalcon\Config
     **/
    private $config;

    /**
     * The authorization server.
     *
     * @param \Ivyhjk\OAuth2\Server\AuthorizationServer
     **/
    private $authorizationServer;

    /**
     * The resource server.
     *
     * @param \Ivyhjk\OAuth2\Server\ResourceServer
     **/
    private $resourceServer;

    /**
     * Repository factory.
     *
     * @var \Ivyhjk\OAuth2\Server\Repository\Factory
     **/
    private $repositoryFactory;

    /**
     * Constructor.
     *
     * @param \Phalcon\Config $configurations
     *
     * @return void
     **/
    public function __construct(\Phalcon\Config $configurations)
    {
        $this->config = $configurations;
    }

    /**
     * Get OAuth2 server configurations.
     *
     * @return \Phalcon\Config
     **/
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the authorization server.
     *
     * @return \Ivyhjk\OAuth2\Server\AuthorizationServer
     **/
    public function getAuthorizationServer()
    {
        if ( ! $this->authorizationServer) {
            $this->authorizationServer = new AuthorizationServer(
                $this->getRepositoryFactory()->build('Client'),
                $this->getRepositoryFactory()->build('AccessToken'),
                $this->getRepositoryFactory()->build('Scope'),
                $this->getConfig()->privateKey,
                $this->getConfig()->publicKey
            );
        }

        return $this->authorizationServer;
    }

    /**
     * Get the resource server.
     *
     * @return \Ivyhjk\OAuth2\Server\ResourceServer
     **/
    public function getResourceServer()
    {
        if ( ! $this->resourceServer) {
            $this->resourceServer = new ResourceServer(
                $this->getRepositoryFactory()->build('AccessToken'),
                $this->getConfig()->publicKey
            );
        }

        return $this->resourceServer;
    }

    public function requestAccessToken()
    {
        $server = $this->getAuthorizationServer();

        $grant = new \Ivyhjk\OAuth2\Server\Grant\Password(
            $this->getRepositoryFactory()->build('User'),
            $this->getRepositoryFactory()->build('RefreshToken')
        );

        $grant->setRefreshTokenTTL(new \DateInterval($this->getConfig()->refresh_token_ttl));

        $server->enableGrantType($grant, new \DateInterval($this->getConfig()->access_token_ttl));

        $di = $this->getDi();

        return $server->respondToAccessTokenRequest($di->get('request'), $di->get('response'));
    }

    public function requestRefreshToken()
    {
        $server = $this->getAuthorizationServer();

        $grant = new \Ivyhjk\OAuth2\Server\Grant\RefreshToken(
            $this->getRepositoryFactory()->build('RefreshToken')
        );

        $grant->setRefreshTokenTTL(new \DateInterval($this->getConfig()->refresh_token_ttl));

        $server->enableGrantType($grant, new \DateInterval($this->getConfig()->access_token_ttl));

        $di = $this->getDi();

        return $server->respondToAccessTokenRequest($di->get('request'), $di->get('response'));
    }

    public function issueAccessToken()
    {
        $server = $this->getResourceServer();

        return $server->validateAuthenticatedRequest($this->getDi()->get('request'));
    }

    public function getRepositoryFactory()
    {
        if ( ! $this->repositoryFactory) {
            $this->repositoryFactory = new \Ivyhjk\OAuth2\Server\Repository\Factory($this->getConfig());
        }

        return $this->repositoryFactory;
    }
}
