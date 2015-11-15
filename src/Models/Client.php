<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class Client extends BaseModel {
    public function initialize()
    {
        $this->setSource('oauth_clients');
    }
}
