<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class GrantScope extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_grant_scopes');
    }
}
