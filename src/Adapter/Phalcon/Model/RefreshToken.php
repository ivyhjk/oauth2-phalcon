<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

class RefreshToken extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_refresh_tokens');
    }
}
