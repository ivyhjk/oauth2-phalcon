<?php

namespace Ivyhjk\OAuth2\Phalcon\Server;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use League\OAuth2\Server\Storage\ScopeInterface;
use League\OAuth2\Server\Storage\ClientInterface;
use League\OAuth2\Server\Storage\SessionInterface;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use League\OAuth2\Server\Exception\InvalidRequestException;

use League\OAuth2\Server\ResourceServer as LeagueResourceServer;

class ResourceServer extends LeagueResourceServer implements InjectionAwareInterface {
    /**
     * Phalcon dependency injector.
     *
     * @var Phalcon\Di\InjectionAwareInterface
     **/
    private $dependencyInjector = null;

    /**
     * Initialise the resource server
     *
     * @param \League\OAuth2\Server\Storage\SessionInterface $sessionStorage
     * @param \League\OAuth2\Server\Storage\AccessTokenInterface$accessTokenStorage
     * @param \League\OAuth2\Server\Storage\ClientInterface $clientStorage
     * @param \League\OAuth2\Server\Storage\ScopeInterface $scopeStorage
     *
     * @return self
     **/
    public function __construct(SessionInterface $sessionStorage, AccessTokenInterface $accessTokenStorage, ClientInterface $clientStorage, ScopeInterface $scopeStorage) {
        return parent::__construct($sessionStorage, $accessTokenStorage, $clientStorage, $scopeStorage);
    }

    /**
     * Set phalcon dependency injector.
     *
     * @param $dependencyInjector \Phalcon\Di\InjectionAwareInterface
     *
     * @return self
     **/
    public function setDi(DiInterface $dependencyInjector)
    {
        $this->dependencyInjector = $dependencyInjector;

        return $this;
    }

    /**
     * Get phalcon dependency injector instance.
     *
     * @return \Phalcon\Di\InjectionAwareInterface
     **/
    public function getDi()
    {
        if ($this->dependencyInjector === null) {
            throw new \Exception('No dependency injector found!.');
        }

        return $this->dependencyInjector;
    }

    /**
     * Reads in the access token from the headers
     *
     * @param bool $headerOnly Limit Access Token to Authorization header
     *
     * @return string
     *
     * @throws \League\OAuth2\Server\Exception\InvalidRequestException Thrown if there is no access token presented
     **/
    public function determineAccessToken($headerOnly = false)
    {
        $request = $this->getDi()->get('request');

        $accessToken = null;

        if ($request->getHeader('Authorization')) {
            dd('REQUEST TOKEN IN HEADER!.');
        } else if ($headerOnly === false) {
            $method = 'get';

            switch ($request->getMethod()) {
                case 'PUT':
                    $method .= 'Put';
                break;
                case 'POST':
                    $method .= 'Post';
                break;
            }

            $accessToken = $request->{$method}($this->tokenKey);
        }

        if ( ! $accessToken) {
            throw new InvalidRequestException('access token');
        }

        return $accessToken;
    }
}
