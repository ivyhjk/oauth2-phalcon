<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class ClientEndpoint extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_client_endpoints');
    }
}
