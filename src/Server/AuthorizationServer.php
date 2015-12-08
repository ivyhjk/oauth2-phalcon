<?php

namespace Ivyhjk\OAuth2\Phalcon\Server;

use League\OAuth2\Server\Exception\InvalidRequestException;
use League\OAuth2\Server\Exception\UnsupportedGrantTypeException;
use League\OAuth2\Server\AuthorizationServer as LeagueAuthorizationServer;

class AuthorizationServer extends LeagueAuthorizationServer {
	/**
	 * Get a parameter from raw body.
	 *
	 * @param string $parameter
	 *
	 * @return mixed [string|int|null] Return null when parameter is not finded. 
	 **/
	public function getParam($parameter, $defaultParam = null)
	{
		$request = $this->getRequest();

		$body = $request->getJsonRawBody();

		if ($body !== null) {
			if (property_exists($body, $parameter) === false) {
				if ($defaultParam !== null) {
					return $defaultParam;
				}

				return null;
			} else {
				if ( ! $body->{$parameter} && $defaultParam !== null) {
					return $defaultParam;
				}
			}
			
			return $body->{$parameter};
		}

		$method = 'get';

		switch ($request->getMethod()) {
			case 'PUT':
				$method .= 'Put';
			break;
			case 'POST':
				$method .= 'Post';
			break;
		}

		$value = $request->{$method}($parameter);

		if ($value === null && $defaultParam !== null) {
			return $defaultParam;
		}

		return $value;
	}

	/**
     * Issue an access token
     *
     * @return array Authorise request parameters
     *
     * @throws
     **/
    public function issueAccessToken()
    {
        $grantType = $this->getParam('grant_type');

        if ($grantType === null) {
            throw new InvalidRequestException('grant_type');
        }

        // Ensure grant type is one that is recognised and is enabled
        if ( ! in_array($grantType, array_keys($this->grantTypes))) {
            throw new UnsupportedGrantTypeException($grantType);
        }

        // Complete the flow
        return $this->getGrantType($grantType)->completeFlow();
    }
}
