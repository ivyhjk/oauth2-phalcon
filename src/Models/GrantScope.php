<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class GrantScope extends BaseModel {
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_grant_scopes');
    }
}
