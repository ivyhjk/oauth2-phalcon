<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class Client extends BaseModel {
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_clients');
    }
}
