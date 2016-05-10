# OAuth2 Server
OAuth 2 server for Phalcon Framework.

Based on:
  - https://github.com/thephpleague/oauth2-server

## Installation

You can install this package into your application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

``` bash
$ composer require ivyhjk/oauth2-phalcon
```

## Notes

* thephpleague/oauth2-server v5 is based on PSR-7 standards, but in the current phalcon version (2.0.11) this standard is not supported, so, i created a new "version" special for this (based on phalcon requests).
* I tested a PSR-7 standard version for phalcon 2.0.x (based on slim 3 HTTP), but it's too slowly compared with this version, based on phalcon requests.

## TODO

* Constant integration with thephpleague/oauth2-server v5
* Testings
* Manuals/Instructions/Wiki
* Eloquent integration (just phalcon models/queries are currently supported)
