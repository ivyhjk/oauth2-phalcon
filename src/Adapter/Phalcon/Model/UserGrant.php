<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class UserGrant extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_user_grants');
    }
}
