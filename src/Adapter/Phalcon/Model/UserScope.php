<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class UserScope extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_user_scopes');
    }
}
