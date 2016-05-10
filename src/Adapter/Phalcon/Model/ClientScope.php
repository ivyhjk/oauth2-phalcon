<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class clientScope extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_client_scopes');
    }
}
