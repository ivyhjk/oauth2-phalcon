<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class Scope extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_scopes');
    }
}
