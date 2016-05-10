<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */
namespace Ivyhjk\OAuth2\Server\AuthorizationValidators;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
/*use Psr\Http\Message\ServerRequestInterface;*/

use Ivyhjk\OAuth2\Server\Contract\AuthorizationValidator;

class BearerTokenValidator implements AuthorizationValidator
{
    use CryptTrait;
    /**
     * @var \League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface
     */
    private $accessTokenRepository;
    /**
     * BearerTokenValidator constructor.
     *
     * @param \League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function __construct(AccessTokenRepositoryInterface $accessTokenRepository)
    {
        $this->accessTokenRepository = $accessTokenRepository;
    }
    /**
     * {@inheritdoc}
     */
    public function validateAuthorization(\Phalcon\Http\RequestInterface $request)
    {
        if ( ! $request->getHeader('authorization')) {
            throw OAuthServerException::accessDenied('Missing "Authorization" header');
        }

        $header = $request->getHeader('authorization');

        $jwt = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $header));

        try {
            // Attempt to parse and validate the JWT
            $token = (new Parser())->parse($jwt);
            if ($token->verify(new Sha256(), $this->publicKey->getKeyPath()) === false) {
                throw OAuthServerException::accessDenied('Access token could not be verified');
            }
            // Ensure access token hasn't expired
            $data = new ValidationData();
            $data->setCurrentTime(time());
            if ($token->validate($data) === false) {
                throw OAuthServerException::accessDenied('Access token is invalid');
            }
            // Check if token has been revoked
            if ($this->accessTokenRepository->isAccessTokenRevoked($token->getClaim('jti'))) {
                throw OAuthServerException::accessDenied('Access token has been revoked');
            }

            // Return the response with additional attributes
            $response = [
                'oauth_access_token_id' => $token->getClaim('jti'),
                'oauth_client_id' => $token->getClaim('aud'),
                'oauth_user_id' => $token->getClaim('sub'),
                'oauth_scopes' => $token->getClaim('scopes'),
            ];

            return $response;
        } catch (\InvalidArgumentException $exception) {
            // JWT couldn't be parsed so return the request as is
            throw OAuthServerException::accessDenied($exception->getMessage());
        }
    }
}
