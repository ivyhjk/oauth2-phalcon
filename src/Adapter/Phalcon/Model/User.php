<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class User extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_users');
    }
}
