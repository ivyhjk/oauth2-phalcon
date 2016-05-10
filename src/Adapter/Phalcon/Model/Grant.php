<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class Grant extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_grants');
    }
}
