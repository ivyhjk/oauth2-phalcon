<?php

/**
 * OAuth 2.0 Password grant.
 *
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */
namespace Ivyhjk\OAuth2\Server\Grant;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;

use Ivyhjk\OAuth2\Server\Contract\ResponseType as ResponseTypeContract;

/**
 * Password grant class.
 */
class Password extends Base
{
    /**
     * Create a new password grant.
     *
     * @param \League\OAuth2\Server\Repositories\UserRepositoryInterface $userRepository
     * @param \League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface $refreshTokenRepository
     *
     * @return void
     **/
    public function __construct(
        UserRepositoryInterface $userRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setUserRepository($userRepository);
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->refreshTokenTTL = new \DateInterval('P1M');
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        \Phalcon\Http\RequestInterface $request,
        ResponseTypeContract $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        $user = $this->validateUser($request, $client);
        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());
        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);
        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);
        return $responseType;
    }

    /**
     * @param \lcon\Http\RequestInterface $request
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     *
     * @return \League\OAuth2\Server\Entities\UserEntityInterface
     **/
    protected function validateUser(\Phalcon\Http\RequestInterface $request, ClientEntityInterface $client)
    {
        $username = $this->getRequestParameter('username', $request);
        if (is_null($username)) {
            throw OAuthServerException::invalidRequest('username', '`%s` parameter is missing');
        }
        $password = $this->getRequestParameter('password', $request);
        if (is_null($password)) {
            throw OAuthServerException::invalidRequest('password', '`%s` parameter is missing');
        }
        $user = $this->userRepository->getUserEntityByUserCredentials(
            $username,
            $password,
            $this->getIdentifier(),
            $client
        );
        if (!$user instanceof UserEntityInterface) {
            $this->getEmitter()->emit(new RequestEvent('user.authentication.failed', $request));
            throw OAuthServerException::invalidCredentials();
        }
        return $user;
    }
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'password';
    }
}
