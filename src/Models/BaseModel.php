<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

use Phalcon\Mvc\Model;

abstract class BaseModel extends Model {
    public function initialize()
    {
        // $this->setConnectionService('Default');
    }
}
