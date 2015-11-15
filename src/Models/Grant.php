<?php

namespace Ivyhjk\OAuth2\Phalcon\Models;

class Grant extends BaseModel {
	public function initialize()
	{
		$this->setSource('oauth_grants');
	}
}
