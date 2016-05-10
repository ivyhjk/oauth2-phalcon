<?php
/**
 * OAuth 2.0 Response Type Interface.
 *
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */
namespace Ivyhjk\OAuth2\Server\Contract;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

use Ivyhjk\OAuth2\Server\Contract\Http\Response;

interface ResponseType
{
    /**
     * @param \League\OAuth2\Server\Entities\AccessTokenEntityInterface $accessToken
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken);
    /**
     * @param \League\OAuth2\Server\Entities\RefreshTokenEntityInterface $refreshToken
     */
    public function setRefreshToken(RefreshTokenEntityInterface $refreshToken);
    /**
     * @param \Ivyhjk\OAuth2\Server\Contract\Http\Response $response
     *
     * @return \Ivyhjk\OAuth2\Server\Contract\Http\Response
     */
    public function generateHttpResponse(Response $response);
}
