<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class AccessToken extends BaseModel {
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_access_tokens');
    }
}
