<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Grant;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

use League\OAuth2\Server\Exception;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Grant\AbstractGrant as AbstractGrantLeague;

/**
 * Abstract grant class
 */
abstract class AbstractGrant extends AbstractGrantLeague implements InjectionAwareInterface
{
    /**
     * Phalcon Dependency Injector.
     *
     * @var \Phalcon\DiInterface
     **/
    private $di = null;

    /**
     * Get Phalcon Dependency Injector.
     *
     * @param \Phalcon\DiInterface $di
     *
     * @return self
     **/
    public function setDi(DiInterface $di)
    {
        $this->di = $di;

        return $this;
    }

    /**
     * Get Phalcon Dependency Injector.
     *
     * @return \Phalcon\DiInterface instance
     **/
    public function getDi()
    {
        return $this->di;
    }
}
