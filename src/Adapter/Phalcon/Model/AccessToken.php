<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

use Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\Scope;
use Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\AccessTokenScope;

class AccessToken extends Model
{
    public function initialize()
    {
        parent::initialize();

        $this->setSource('oauth_access_tokens');

        $this->hasManyToMany(
            'id',
            AccessTokenScope::class,
            'access_token_id',
            'scope_id',
            Scope::class,
            'id',
            ['alias' => 'scopes']
        );
    }
}
