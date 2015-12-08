<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class SessionScope extends BaseModel {
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_session_scopes');

        $this->skipAttributesOnCreate(['id']);
    }

    // Fix https://github.com/phalcon/cphalcon/issues/1134
    public function getSequenceName() {
        return null;
    }
}
