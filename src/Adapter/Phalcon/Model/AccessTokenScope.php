<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

use Ivyhjk\OAuth2\Server\Adapter\Phalcon\Scope;
use Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model\AccessToken;

class AccessTokenScope extends Model
{
    public function initialize()
    {
        $this->setSource('oauth_access_token_scopes');

        $this->belongsTo('scope_id', Scope::class, 'id');
        $this->belongsTo('access_token_id', AccessToken::class, 'id');
    }
}
