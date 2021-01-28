# WsSecurity

> WsSecurity adds the WSSE authentication header to your SOAP request

[![License](https://poser.pugx.org/wsdltophp/wssecurity/license)](https://packagist.org/packages/wsdltophp/wssecurity)
[![Latest Stable Version](https://poser.pugx.org/wsdltophp/wssecurity/version.png)](https://packagist.org/packages/wsdltophp/wssecurity)
[![Build Status](https://travis-ci.com/WsdlToPhp/WsSecurity.svg)](https://travis-ci.com/github/WsdlToPhp/WsSecurity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/WsdlToPhp/WsSecurity/badges/quality-score.png)](https://scrutinizer-ci.com/g/WsdlToPhp/WsSecurity/)
[![Code Coverage](https://scrutinizer-ci.com/g/WsdlToPhp/WsSecurity/badges/coverage.png)](https://scrutinizer-ci.com/g/WsdlToPhp/WsSecurity/)
[![Total Downloads](https://poser.pugx.org/wsdltophp/wssecurity/downloads)](https://packagist.org/packages/wsdltophp/wssecurity)
[![StyleCI](https://styleci.io/repos/43811404/shield)](https://styleci.io/repos/43811404)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1cc28292-0f49-47eb-b2ca-4bdd6c0223f1/mini.png)](https://insight.sensiolabs.com/projects/1cc28292-0f49-47eb-b2ca-4bdd6c0223f1)

## How to use it
This repository contains multiple classes that may be used indepdently but for now it is easier/better to only use the WsSecurity class.

The WsSecurity class provides a static method that takes the parameters that should suffice to create your Ws-Security Username Authentication header required in your SOAP request.

Concretly, you must include this repository in your project using composer (`composer require wsdltophp/wssecurity`) then use it such as:

```php
use WsdlToPhp\WsSecurity\WsSecurity;

/**
 * @var \SoapHeader
 */
$soapHeader = WsSecurity::createWsSecuritySoapHeader('login', 'password', true);
/**
 * Send the request
 */
$soapClient = new \SoapClient('wsdl_url');
$soapClient->__setSoapHeaders($soapHeader);
$soapClient->__soapCall('echoVoid', []);
```

The `WsSecurity::createWsSecuritySoapHeader` parameters are defined in this order `($username, $password, $passwordDigest = false, $addCreated = 0, $addExpires = 0, $returnSoapHeader = true, $mustunderstand = false, $actor = null, $usernameId = null, $addNonce = true)`:

- **$username**: your login/username
- **$password**: your password
- **$passwordDigest**: set it to `true` if your password must be encrypted
- **$addCreated**: set it to the time you created this header using the PHP [time](http://php.net/manual/en/function.time.php) function for example, otherwise pass 0
- **$addExpires**: set it to the number of seconds in which the header will expire, 0 otherwise
- **$returnSoapHeader**: set it to false if you want to get the [\SoapVar](http://php.net/manual/en/class.soapvar.php) object that is used to create the [\SoapHeader](http://php.net/manual/en/class.soapheader.php) object, then you'll have to use to create by yourself the [\SoapHeader](http://php.net/manual/en/class.soapheader.php) object
- **$mustunderstand**: classic option of the [\SoapClient](http://php.net/manual/en/soapclient.soapclient.php) class
- **$actor**: classic option of the [\SoapClient](http://php.net/manual/en/soapclient.soapclient.php) class
- **$usernameId**: the id to attach to the UsernameToken element, optional
- **$addNonce**: _true_ by default, if true, it adds the nonce element to the header, if false it does not add the nonce element to the header 

## Testing using [Docker](https://www.docker.com/)
Thanks to the [Docker image](https://hub.docker.com/r/splitbrain/phpfarm) of [phpfarm](https://github.com/fpoirotte/phpfarm), tests can be run locally under *any* PHP version using the cli:
- php-7.4

First of all, you need to create your container which you can do using [docker-compose](https://docs.docker.com/compose/) by running the below command line from the root directory of the project:
```bash
$ docker-compose up -d --build
```

You then have a container named `ws_security` in which you can run `composer` commands and `php cli` commands such as:
```bash
# install deps in container (using update ensure it does use the composer.lock file if there is any)
$ docker exec -it ws_security php-7.4 /usr/bin/composer update
# run tests in container
$ docker exec -it ws_security php-7.4 -dmemory_limit=-1 vendor/bin/phpunit
```

## FAQ

If you have a question, feel free to [create an issue](https://github.com/WsdlToPhp/WsSecurity/issues/new).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
