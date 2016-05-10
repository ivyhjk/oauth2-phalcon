<?php

/**
 * OAuth 2.0 Bearer Token Type.
 *
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */
namespace Ivyhjk\OAuth2\Server\ResponseType;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

class BearerTokenResponse extends Base
{
    /**
     * {@inheritdoc}
     */
    public function generateHttpResponse(\Ivyhjk\OAuth2\Server\Contract\Http\Response $response)
    {
        $jwtAccessToken = $this->accessToken->convertToJWT($this->privateKey);
        $expireDateTime = $this->accessToken->getExpiryDateTime()->getTimestamp();

        $responseParams = [
            'token_type'   => 'Bearer',
            'expires_in'   => $expireDateTime - (new \DateTime())->getTimestamp(),
            'access_token' => (string) $jwtAccessToken,
        ];

        if ($this->refreshToken instanceof RefreshTokenEntityInterface) {
            $refreshToken = $this->encrypt(
                json_encode(
                    [
                        'client_id'        => $this->accessToken->getClient()->getIdentifier(),
                        'refresh_token_id' => $this->refreshToken->getIdentifier(),
                        'access_token_id'  => $this->accessToken->getIdentifier(),
                        'scopes'           => $this->accessToken->getScopes(),
                        'user_id'          => $this->accessToken->getUserIdentifier(),
                        'expire_time'      => $this->refreshToken->getExpiryDateTime()->getTimestamp(),
                    ]
                )
            );
            $responseParams['refresh_token'] = $refreshToken;
        }

        $response = $response
            ->setStatusCode(200)
            ->addHeader([
                'pragma' => 'no-cache',
                'cache-control' => 'no-store',
                'content-type' => 'application/json; charset=UTF-8'
            ])
            ->setBody($responseParams);

        return $response;
    }
}
