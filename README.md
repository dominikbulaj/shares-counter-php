Shares Counter
==============

Simple PHP solution for getting number of URL shares on most popular social networks.

[![Build Status](https://travis-ci.org/dominikbulaj/shares-counter-php.svg?branch=master)](https://travis-ci.org/dominikbulaj/shares-counter-php)

Requirements
------------
* PHP 5.4+
* CURL extension

Features
--------
Check provided URL in popular social networks and return array with number of shares in each one.

Supported social networks:

* Facebook
* Twitter
* Google+
* Pinterest
* LinkedIn
* StumbleUpon
* Odnoklassniki (Russia) - disabled by default
* VKontakte (Russia) - disabled by default 
* Wykop (Poland) - disabled by default

Example output:
```
array(6) {
  ["facebook"]=>
  int(155)
  ["twitter"]=>
  int(260628)
  ["google_plus"]=>
  int(3342936)
  ["pinterest"]=>
  int(27)
  ["linkedin"]=>
  int(2938)
  ["stumbleupon"]=>
  int(119288)
}
```

## Usage

**Get number of shares in 6 enabled by default networks**

```php
$url = 'http://www.example.com';

$shares = new \SharesCounter\SharesCounter($url);
$counts = $shares->getShares();
var_export($counts); 
```
**We can also get shares counts in any of networks** (let's take Facebook & Twitter)

```php
$url = 'http://www.example.com';

$shares = new \SharesCounter\SharesCounter($url);
// getShares() accepts optional array with supported network(s)
$counts = $shares->getShares([\SharesCounter\Networks::NETWORK_FACEBOOK, \SharesCounter\Networks::NETWORK_TWITTER]);
var_export($counts); 
```
**To easily get list of supported networks**

```php
$networks = new \SharesCounter\Networks();
$availableNetworks = $networks->getAvailableNetworks();
var_export($availableNetworks); // lists 9 networks

// NOTE if we want to return just enabled by default networks we need pass TRUE to getAvailableNetworks() to filter list
$enabledNetworks = $networks->getAvailableNetworks(true);
var_export($enabledNetworks); // lists 6 networks
```

**Finally, let say we want to get shares from network that's disabled by default** (just provide this network while calling `getShares()`)

```php
$url = 'http://pokazywarka.pl/margaryna/';
$shares = new \SharesCounter\SharesCounter($url);
$counts = $shares->getShares([\SharesCounter\Networks::NETWORK_WYKOP]);
var_export($counts);
```

## Configuration
Configuration for networks (URLs, parsing output, status (enabled/disabled) and optional timeout of request is done in
`\SharesCounter\Networks::__construct()`.

Each network configuration is an array and has at least 3 key-values:
* url (endpoint to fetch, url is replaced by `%s` so it can be easily injected using `sprintf` function)
* enabled (flag that determines if we can use it by default or not)
* parse (lambda function with just one parameter `$response` used to get number of shares)
Optionally we configuration can have 4th value:
* timeout (number of milliseconds to wait for response while doing CURL request)

Default timeout for all networks (without own timeout configuration) is stored in `\SharesCounter\Networks::DEFAULT_TIMEOUT` constant and equals 2000.

Why constructor and not class property?
Because I attached to any network configuration (URL, status) lambda function that is responsible for parsing response.
We can't do this in class properties ([more on this](http://php.net/manual/en/language.oop5.properties.php))

## License
MIT