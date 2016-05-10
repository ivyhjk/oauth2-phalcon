<?php

namespace Ivyhjk\OAuth2\Server\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{
    use EntityTrait;

    private $name;
    private $secret;
    private $redirectUri;

    /**
     * Get the client's name.
     *
     * @return string
     **/
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the client's name.
     *
     * @param string $name
     **/
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $secret
     **/
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get the hashed client secret
     *
     * @return string
     **/
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set the client's redirect uri.
     *
     * @param string $redirectUri
     **/
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * Returns the registered redirect URI.
     *
     * @return string
     **/
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Returns true if the client is capable of keeping it's secrets secret.
     *
     * @return bool
     **/
    public function canKeepASecret()
    {
        return $this->secret !== null;
    }
}
