<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class ClientGrant extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_client_grants');
    }
}
