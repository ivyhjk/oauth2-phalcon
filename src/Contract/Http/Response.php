<?php

namespace Ivyhjk\OAuth2\Server\Contract\Http;

interface Response
{
    /**
     * Set response status code.
     *
     * @param int $status_code
     *
     * @return self
     **/
    public function setStatusCode($status_code);

    /**
     * Get current response status code.
     *
     * @return int
     **/
    public function getStatusCode();

    /**
     * Set response body.
     *
     * @param mixed $body
     *
     * @return self
     **/
    public function setBody($body);

    /**
     * Get response body.
     *
     * @return mixed
     **/
    public function getBody();

    public function addHeader(array $header);
    public function setHeaders(array $headers);
    public function getHeaders();

    public function setStatus($status);
    public function getStatus();

    public function addError($error);
    public function setErrors($errors);
    public function getErrors();
}
