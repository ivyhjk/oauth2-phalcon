<?php

namespace Ivyhjk\OAuth2\Server\Repository;

class Factory
{
    /**
     * Repositories configurations.
     *
     * @var \Phalcon\Config
     **/
    private $config;

    /**
     * Create a new factory.
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
     * Get repostories configuration.
     *
     * @return \Phalcon\Config
     **/
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get repositories namespace.
     *
     * @return string
     **/
    private function getNamespace()
    {
        $adapter = $this->getConfig()->repository_adapter;

        $name = '\\Ivyhjk\\OAuth2\\Server\\Adapter\\' . $adapter . '\\Repository';

        return $name;
    }

    /**
     * Generate a new repository
     *
     * @param string $repositoryName
     *
     * @return \Ivyhjk\OAuth2\Server\Contract\Repository
     **/
    public function build($repositoryName)
    {
        $className = $this->getNamespace() . '\\' . $repositoryName;

        return new $className($this->getConfig());
    }
}
