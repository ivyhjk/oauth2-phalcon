<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class Session extends BaseModel {
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_sessions');
    }
}
