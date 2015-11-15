<?php

namespace Ivyhjk\OAuth2\Phalcon\Server\Grant;

use League\OAuth2\Server\Util\SecureKey;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Exception\InvalidClientException;
use League\OAuth2\Server\Exception\InvalidRequestException;
use League\OAuth2\Server\Event\UserAuthenticationFailedEvent;
use League\OAuth2\Server\Exception\InvalidCredentialsException;
use League\OAuth2\Server\Event\ClientAuthenticationFailedEvent;
use League\OAuth2\Server\Grant\PasswordGrant as PasswordGrantLeague;

/**
 * Password grant class
 */
class PasswordGrant extends PasswordGrantLeague
{
    /**
     * Complete the password grant
     *
     * @return array
     *
     * @throws \League\OAuth2\Server\Exception\InvalidClientException
     * @throws \League\OAuth2\Server\Exception\InvalidRequestException
     */
    public function completeFlow()
    {
        // Get the required params
        $clientId = $this->server->getParam('client_id');


        if ($clientId === null) {
            throw new InvalidRequestException('client_id');
        }

        $clientSecret = $this->server->getParam('client_secret', $this->server->getRequest()->getHeader('PHP_AUTH_PW'));

        if ( ! $clientSecret) {
            throw new InvalidRequestException('client_secret');
        }

        // Validate client ID and client secret
        $client = $this->server->getClientStorage()->get(
            $clientId,
            $clientSecret,
            null,
            $this->getIdentifier()
        );

        if (($client instanceof ClientEntity) === false) {
            // $this->server->getEventEmitter()->emit(new ClientAuthenticationFailedEvent($this->server->getRequest()));
            throw new InvalidClientException();
        }

        $username = $this->server->getParam('username');

        if ($username === null) {
            throw new InvalidRequestException('username');
        }

        $password = $this->server->getParam('password');

        if ($password === null) {
            throw new InvalidRequestException('password');
        }

        // Check if user's username and password are correct
        $userId = call_user_func($this->getVerifyCredentialsCallback(), $username, $password);

        if ($userId === false) {
            // $this->server->getEventEmitter()->emit(new UserAuthenticationFailedEvent($this->server->getRequest()));
            throw new InvalidCredentialsException();
        }

        // Validate any scopes that are in the request
        $scopeParam = $this->server->getParam('scope', '');
        $scopes = $this->validateScopes($scopeParam, $client);


        // Create a new session
        $session = new SessionEntity($this->server);
        $session->setOwner('user', $userId);
        $session->associateClient($client);

        // Generate an access token
        $accessToken = new AccessTokenEntity($this->server);
        $accessToken->setId(SecureKey::generate());
        $accessToken->setExpireTime($this->getAccessTokenTTL() + time());

        // Associate scopes with the session and access token
        foreach ($scopes as $scope) {
            $session->associateScope($scope);
        }

        foreach ($session->getScopes() as $scope) {
            $accessToken->associateScope($scope);
        }

        $tokenType = $this->server->getTokenType();

        $tokenType->setSession($session);
        $tokenType->setParam('access_token', $accessToken->getId());
        $tokenType->setParam('expires_in', $this->getAccessTokenTTL());

        // Associate a refresh token if set
        if ($this->server->hasGrantType('refresh_token')) {
            $refreshToken = new RefreshTokenEntity($this->server);
            $refreshToken->setId(SecureKey::generate());
            $refreshToken->setExpireTime($this->server->getGrantType('refresh_token')->getRefreshTokenTTL() + time());
            $this->server->getTokenType()->setParam('refresh_token', $refreshToken->getId());
        }

        // Save everything
        $session->save();
        $accessToken->setSession($session);
        $accessToken->save();

        if ($this->server->hasGrantType('refresh_token')) {
            $refreshToken->setAccessToken($accessToken);
            $refreshToken->save();
        }

        return $this->server->getTokenType()->generateResponse();
    }
}
