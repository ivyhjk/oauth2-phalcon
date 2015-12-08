<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class ClientScope extends BaseModel {
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_client_scopes');
    }
}
