<?php

namespace Ivyhjk\Oauth2\Phalcon\Server\Storage;

use League\OAuth2\Server\Storage\AbstractStorage;

abstract class BaseStorage extends AbstractStorage {
    private $database = null;

    public function __construct($database)
    {
        $this->database = $database;
    }

    protected final function getDatabase()
    {
        if ( ! $this->database) {
            throw new \Exception('Database can not be found.');
        }

        return $this->database;
    }
}
