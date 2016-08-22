<?php
/**
 * Examples of ShareCouners usage
 * @author Dominik Bułaj <dominik@bulaj.com>
 */
include '../src/SharesCounter.php';
include '../src/Networks.php';
include '../src/Exception.php';

$url = 'http://www.huffingtonpost.com';

// 1. return shares from Facebook and Twitter
$shares = new \SharesCounter\SharesCounter($url);
$counts = $shares->getShares([\SharesCounter\Networks::NETWORK_FACEBOOK, \SharesCounter\Networks::NETWORK_TWITTER]);
var_dump($counts);

// 2. return shares from all available networks
$shares = new \SharesCounter\SharesCounter($url);
$counts = $shares->getShares([]);
var_dump($counts);

// 3. return shares from disabled by default network
$url = 'http://www.moy-rebenok.ru';
$shares = new \SharesCounter\SharesCounter($url);
$counts = $shares->getShares([\SharesCounter\Networks::NETWORK_VK, \SharesCounter\Networks::NETWORK_ODNOKLASSNIKI]);
var_dump($counts);

// 4. wykop.pl
$url = 'http://pokazywarka.pl/margaryna/';
$shares = new \SharesCounter\SharesCounter($url);
$counts = $shares->getShares([\SharesCounter\Networks::NETWORK_WYKOP]);
var_dump($counts);

// 4. helper method - return list of available networks
$networks = new \SharesCounter\Networks();
$availableNetworks = $networks->getAvailableNetworks();
var_export($availableNetworks);