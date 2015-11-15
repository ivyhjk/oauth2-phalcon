<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class Scope extends BaseModel {
	public function initialize()
	{
		$this->setSource('oauth_scopes');
	}
}
