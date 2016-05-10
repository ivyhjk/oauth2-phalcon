<?php
/**
 * OAuth 2.0 Refresh token grant.
 *
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */
namespace Ivyhjk\OAuth2\Server\Grant;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
/*use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;*/
/*use Psr\Http\Message\ServerRequestInterface;*/

use Phalcon\Http\RequestInterface as RequestContract;
use Ivyhjk\OAuth2\Server\Contract\ResponseType as ResponseTypeContract;

/**
 * Refresh token grant.
 */
class RefreshToken extends Base
{
    /**
     * @param \League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->refreshTokenTTL = new \DateInterval('P1M');
    }
    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        RequestContract $request,
        ResponseTypeContract $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $oldRefreshToken = $this->validateOldRefreshToken($request, $client->getIdentifier());
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        // If no new scopes are requested then give the access token the original session scopes
        if (count($scopes) === 0) {
            $scopes = array_map(function ($scopeId) use ($client) {
                $scope = $this->scopeRepository->getScopeEntityByIdentifier($scopeId);
                if (!$scope instanceof ScopeEntityInterface) {
                    // @codeCoverageIgnoreStart
                    throw OAuthServerException::invalidScope($scopeId);
                    // @codeCoverageIgnoreEnd
                }
                return $scope;
            }, $oldRefreshToken['scopes']);
        } else {
            // The OAuth spec says that a refreshed access token can have the original scopes or fewer so ensure
            // the request doesn't include any new scopes
            foreach ($scopes as $scope) {
                if (in_array($scope->getIdentifier(), $oldRefreshToken['scopes']) === false) {
                    throw OAuthServerException::invalidScope($scope->getIdentifier());
                }
            }
        }
        // Expire old tokens
        $this->accessTokenRepository->revokeAccessToken($oldRefreshToken['access_token_id']);
        $this->refreshTokenRepository->revokeRefreshToken($oldRefreshToken['refresh_token_id']);
        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $oldRefreshToken['user_id'], $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);
        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);
        return $responseType;
    }
    /**
     * @param \Phalcon\Http\RequestInterface $request
     * @param string $clientId
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     *
     * @return array
     */
    protected function validateOldRefreshToken(RequestContract $request, $clientId)
    {
        $encryptedRefreshToken = $this->getRequestParameter('refresh_token', $request);
        if (is_null($encryptedRefreshToken)) {
            throw OAuthServerException::invalidRequest('refresh_token');
        }
        // Validate refresh token
        try {
            $refreshToken = $this->decrypt($encryptedRefreshToken);
        } catch (\LogicException $e) {
            throw OAuthServerException::invalidRefreshToken('Cannot decrypt the refresh token');
        }
        $refreshTokenData = json_decode($refreshToken, true);
        if ($refreshTokenData['client_id'] !== $clientId) {
            $this->getEmitter()->emit(new RequestEvent('refresh_token.client.failed', $request));
            throw OAuthServerException::invalidRefreshToken('Token is not linked to client');
        }
        if ($refreshTokenData['expire_time'] < time()) {
            throw OAuthServerException::invalidRefreshToken('Token has expired');
        }
        if ($this->refreshTokenRepository->isRefreshTokenRevoked($refreshTokenData['refresh_token_id']) === true) {
            throw OAuthServerException::invalidRefreshToken('Token has been revoked');
        }
        return $refreshTokenData;
    }
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'refresh_token';
    }
}
