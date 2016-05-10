<?php

namespace Ivyhjk\OAuth2\Server\Contract;

interface Repository
{
    /**
     * Create a new repository with configs.
     *
     * @param \Phalcon\Config $config
     *
     * @return void
     **/
    public function __construct(\Phalcon\Config $config);

    /**
     * Get repository configurations.
     *
     * @return \Phalcon\Config
     **/
    public function getConfig();
}
