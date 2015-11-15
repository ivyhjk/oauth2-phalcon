<?php

namespace Ivyhjk\OAuth2\Phalcon\Server;

use Phalcon\Mvc\User\Plugin;

use Ivyhjk\OAuth2\Phalcon\Server\ResourceServer;
use Ivyhjk\OAuth2\Phalcon\Server\AuthorizationServer;

class Authorizer extends Plugin {
    /**
     * Resource server.
     *
     * @var \Ivyhjk\OAuth2\Phalcon\Server\ResourceServer
     **/
    private $ResourceServer = null;

    /**
     * Authorization server.
     *
     * @var \Ivyhjk\OAuth2\Phalcon\Server\AuthorizationServer
     **/
    private $AuthorizationServer = null;

    // Storages
    private $ScopeStorage = null;
    private $ClientStorage = null;
    private $SessionStorage = null;
    private $AccessTokenStorage = null;
    private $RefreshTokenStorage = null;
    // End Storages

    /**
     * Instance of a connection class to retrieve data.
     *
     * @var object
     **/
    private $database = null;

    /**
     * Configurations.
     *
     * @var string
     **/
    private $config = [];

    /**
     * Constructor
     *
     * @param object $database An instance of database adapter to retrieve data from database.
     *
     * @return void
     **/
    public function __construct($database, $configurations)
    {
        $this->database = $database;

        // Merge default configs with user configurations.
        $this->config = array_merge($this->config, (array) $configurations);

        // Setup configs.
        // Default storage adapter namespace.
        $this->setStorageAdapter("\\Ivyhjk\\OAuth2\\Phalcon\\Server\\Storage\\{$this->config['storage']}");
    }

    private function setStorageAdapter($adapterNamespace)
    {
        $this->storageAdapter = $adapterNamespace;

        return $this;
    }

    /**
     * Return an instance of Resource Server for requests.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\ResourceServer
     **/
    public function getResourceServer()
    {
        if ($this->ResourceServer !== null) {
            return $this->ResourceServer;
        }

        $this->ResourceServer = new ResourceServer(
            $this->getSessionStorage(),
            $this->getAccessTokenStorage(),
            $this->getClientStorage(),
            $this->getScopeStorage()
        );

        if ( ! $this->ResourceServer instanceof \Phalcon\Di\InjectionAwareInterface) {
            throw new \Exception('Resource server may be compatible with Phalcon dependency injector');
        }

        // Setup dependency injector.
        $this->ResourceServer->setDi($this->getDi());

        return $this->ResourceServer;
    }

    /**
     * Return an instance of Authorization Server for requests.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\AuthorizationServer
     **/
    public function getAuthorizationServer()
    {
        if ($this->AuthorizationServer !== null) {
            return $this->AuthorizationServer;
        }

        $this->AuthorizationServer = new AuthorizationServer();

        // Set storages to authorization server.
        $this->AuthorizationServer->setScopeStorage($this->getScopeStorage());
        $this->AuthorizationServer->setClientStorage($this->getClientStorage());
        $this->AuthorizationServer->setSessionStorage($this->getSessionStorage());
        // $this->AuthorizationServer->setAuthCodeStorage($this->getAuthCodeStorage());
        $this->AuthorizationServer->setAccessTokenStorage($this->getAccessTokenStorage());
        $this->AuthorizationServer->setRefreshTokenStorage($this->getRefreshTokenStorage());

        // Finally enable all grants from config.
        return $this->enableGrants();
    }

    /**
     * Enable all available grants for Authorization server.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\AuthorizationServer
     **/
    public function enableGrants()
    {
        $authorizationServer = $this->getAuthorizationServer();

        foreach ($this->config['grants'] as $name => $class) {
            $isObject = is_object($class);

            if ($isObject && isset($class->class)) {
                $grant = new $class->class();
            } else if (is_string($class)) {
                $grant = new $class();
            }

            $authorizationServer->addGrantType($grant);

            if ($grant instanceof \League\OAuth2\Server\Grant\PasswordGrant) {
                if ($isObject) {
                    $callback = $class->callback;

                    if ( ! $callback) {
                        throw new \Exception('A credentialas verify callback is necesary!.');
                    }

                    $grant->setVerifyCredentialsCallback($class->callback);
                } else {
                    throw new \Exception('A credentialas verify callback is necesary!.');
                }
            }
        }

        return $authorizationServer;
    }

    /**
     * Return a scope storage instance.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\Storage\Scope
     **/
    public function getScopeStorage()
    {
        if ($this->ScopeStorage !== null) {
            return $this->ScopeStorage;
        }

        $storageClass = "{$this->storageAdapter}\\Scope";
        
        return new $storageClass($this->database);
    }

    /**
     * Return a client storage instance.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\Storage\Client
     **/
    public function getClientStorage()
    {
        if ($this->ClientStorage !== null) {
            return $this->ClientStorage;
        }

        $storageClass = "{$this->storageAdapter}\\Client";

        return new $storageClass($this->database, $this->config['limit_clients_to_grants']);
    }

    /**
     * Return a session storage instance.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\Storage\Session
     **/
    public function getSessionStorage()
    {
        if ($this->SessionStorage !== null) {
            return $this->SessionStorage;
        }

        $storageClass = "{$this->storageAdapter}\\Session";
        
        return new $storageClass($this->database);
    }

    /**
     * Return an acces token storage instance.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\Storage\AccessToken
     **/
    public function getAccessTokenStorage()
    {
        if ($this->AccessTokenStorage !== null) {
            return $this->AccessTokenStorage;
        }

        $storageClass = "{$this->storageAdapter}\\AccessToken";
        
        return new $storageClass($this->database);
    }

    /**
     * Return an refresh token storage instance.
     *
     * @return \Ivyhjk\OAuth2\Phalcon\Server\Storage\RefreshToken
     **/
    public function getRefreshTokenStorage()
    {
        if ($this->RefreshTokenStorage !== null) {
            return $this->RefreshTokenStorage;
        }

        $storageClass = "{$this->storageAdapter}\\RefreshToken";
        
        return new $storageClass($this->database);
    }
}
