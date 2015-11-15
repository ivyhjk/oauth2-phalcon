<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class Session extends BaseModel {
	public function initialize()
	{
		$this->setSource('oauth_sessions');
	}
}
