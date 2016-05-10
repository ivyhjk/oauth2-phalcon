<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class Client extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_clients');
    }
}
