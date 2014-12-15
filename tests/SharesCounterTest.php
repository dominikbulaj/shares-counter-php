<?php

namespace SharesCounter\Tests;
use SharesCounter\SharesCounter;

include '../src/SharesCounter.php';
include '../src/Networks.php';
include '../src/Exception.php';

/**
 *
 * @author Dominik Bułaj <dominik@bulaj.com>
 */
class SharesCounterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SharesCounter\Networks
     */
    protected $_sharesCounter;

    /**
     * @expectedException \SharesCounter\Exception
     */
    public function testMissingUrlException()
    {
        new SharesCounter('');
    }
    /**
     * @expectedException \SharesCounter\Exception
     */
    public function testIncorrectUrlException()
    {
        new SharesCounter('abc:defgi.jk');
    }

    public function testGetShares()
    {
        $url = 'http://www.huffingtonpost.com';

        // just 2 networks
        $shares = new SharesCounter($url);
        $counts = $shares->getShares([\SharesCounter\Networks::NETWORK_FACEBOOK, \SharesCounter\Networks::NETWORK_TWITTER]);

        $this->assertInternalType('array', $counts);
        $this->assertNotEmpty($counts);
        $this->assertEquals(2, count($counts));
        $this->assertArrayHasKey(\SharesCounter\Networks::NETWORK_FACEBOOK, $counts);
        $this->assertArrayHasKey(\SharesCounter\Networks::NETWORK_TWITTER, $counts);

        // all networks
        $counts = $shares->getShares();
        $this->assertInternalType('array', $counts);
        $this->assertNotEmpty($counts);
        $this->assertEquals(6, count($counts)); // 6 is available by default

        // counter value must be integer type
        $this->assertInternalType('integer', reset($counts));
        $this->assertGreaterThan(0, reset($counts));
    }
}
