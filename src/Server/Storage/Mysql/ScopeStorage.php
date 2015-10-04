<?php

namespace Ivyhjk\Oauth2\Phalcon\Server\Storage;

use League\OAuth2\Server\Storage\ScopeInterface;

class ScopeStorage extends BaseStorage implements ScopeInterface {
    /**
     * Return information about a scope
     *
     * @param string $scope     The scope
     * @param string $grantType The grant type used in the request (default = "null")
     * @param string $clientId  The client sending the request (default = "null")
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity | null
     */
    public function get($scope, $grantType = null, $clientId = null)
    {
    	dd(1);
    }
}
