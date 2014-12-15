<?php

namespace SharesCounter\Tests;
use SharesCounter\Networks;

/**
 *
 * @author Dominik Bułaj <dominik@bulaj.com>
 */
class NetworksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SharesCounter\Networks
     */
    protected $_networks;

    public function __construct()
    {
        $this->_networks = new Networks();
    }

    public function testAvailableNetworks()
    {
        $networks = $this->_networks->getAvailableNetworks();
        $this->assertInternalType('array', $networks);
        $this->assertNotEmpty($networks);
        $this->assertEquals(9, count($networks));

        // check only available networks
        $networks = $this->_networks->getAvailableNetworks(true);
        $this->assertInternalType('array', $networks);
        $this->assertNotEmpty($networks);
        $this->assertEquals(6, count($networks));
        $this->assertArrayNotHasKey(\SharesCounter\Networks::NETWORK_VK, $networks);
        $this->assertArrayNotHasKey(\SharesCounter\Networks::NETWORK_ODNOKLASSNIKI, $networks);
        $this->assertArrayNotHasKey(\SharesCounter\Networks::NETWORK_WYKOP, $networks);
    }

    public function testGetNetworkConfigs()
    {
        $configs = $this->_networks->getNetworkConfigs();
        $this->assertInternalType('array', $configs);
        $this->assertNotEmpty($configs);
        $this->assertEquals(6, count($configs)); // 6 because returns only available networks!
        $this->assertArrayNotHasKey(\SharesCounter\Networks::NETWORK_VK, $configs);
        $this->assertArrayNotHasKey(\SharesCounter\Networks::NETWORK_ODNOKLASSNIKI, $configs);
        $this->assertArrayNotHasKey(\SharesCounter\Networks::NETWORK_WYKOP, $configs);

        // check Facebook and Twitter only
        $configs = $this->_networks->getNetworkConfigs([\SharesCounter\Networks::NETWORK_FACEBOOK, \SharesCounter\Networks::NETWORK_TWITTER]);
        $this->assertInternalType('array', $configs);
        $this->assertNotEmpty($configs);
        $this->assertEquals(2, count($configs));
        $this->assertArrayHasKey(\SharesCounter\Networks::NETWORK_FACEBOOK, $configs);
        $this->assertArrayHasKey(\SharesCounter\Networks::NETWORK_TWITTER, $configs);
    }
}
