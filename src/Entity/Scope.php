<?php

namespace Ivyhjk\OAuth2\Server\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class Scope implements ScopeEntityInterface
{
    use EntityTrait;

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
