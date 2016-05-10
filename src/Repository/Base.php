<?php

namespace Ivyhjk\OAuth2\Server\Repository;

abstract class Base implements \Ivyhjk\OAuth2\Server\Contract\Repository
{
    /**
     * Repository configurations.
     *
     * @var \Phalcon\Config
     **/
    private $config;

    /**
     * Create a new repository
     *
     * @param \Phalcon\Config $config
     *
     * @return void
     **/
    public function __construct(\Phalcon\Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get repository configurations.
     *
     * @return \Phalcon\Config
     **/
    public function getConfig()
    {
        return $this->config;
    }
}
