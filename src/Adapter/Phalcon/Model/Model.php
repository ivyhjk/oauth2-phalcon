<?php

namespace Ivyhjk\OAuth2\Server\Adapter\Phalcon\Model;

use Phalcon\Mvc\Model\Behavior\Timestampable;

abstract class Model extends \Phalcon\Mvc\Model
{
    public function initialize()
    {
        // $this->addBehavior(new Timestampable([
        //     'beforeCreate' => [
        //         'field' => 'created_at',
        //         // 'value' => date('Y-m-d H:i:s')
        //         'value' => time()
        //     ]
        // ]));
    }
}
