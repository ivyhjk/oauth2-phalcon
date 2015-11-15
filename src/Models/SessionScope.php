<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class SessionScope extends BaseModel {
	public function initialize()
	{
		$this->setSource('oauth_session_scopes');
	}
}
