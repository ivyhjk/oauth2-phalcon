<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class AccessTokenScope extends BaseModel {
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_access_token_scopes');
    }
}
