<?php
/**
 *
 * @author Dominik Bułaj <dominik@bulaj.com>
 */


namespace SharesCounter;

/**
 * Class Networks
 * Configuration for social networks
 *
 * @package ShareCounts
 */
final class Networks
{
    const DEFAULT_TIMEOUT = 2000; // default timeout

    const NETWORK_FACEBOOK = 'facebook';
    const NETWORK_TWITTER = 'twitter';
    const NETWORK_GOOGLE_PLUS = 'google_plus';
    const NETWORK_LINKEDIN = 'linkedin';
    const NETWORK_PINTEREST = 'pinterest';
    const NETWORK_STUMBLEUPON = 'stumbleupon';
    const NETWORK_VK = 'vk';
    const NETWORK_ODNOKLASSNIKI = 'odnoklassniki';
    const NETWORK_WYKOP = 'wykop';

    const NETWORK_ENABLED = 1;
    const NETWORK_DISABLED = 0;

    /**
     * @var array
     */
    private $_config = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // set up config
        $this->_config = [
            self::NETWORK_FACEBOOK      => [
                'url'     => 'http://graph.facebook.com/?id=%s',
                'enabled' => self::NETWORK_ENABLED,
                'parse' => function($response) {
                    $resObj = json_decode($response);
                    return @$resObj->shares->share_count ? @$resObj->shares->share_count ?: 0 : 0;
                },
            ],
            self::NETWORK_TWITTER       => [
                'url'     => 'http://urls.api.twitter.com/1/urls/count.json?url=%s',
                'enabled' => self::NETWORK_ENABLED,
                'parse' => function($response) {
                    return @json_decode($response)->count ?: 0;
                },
            ],
            self::NETWORK_GOOGLE_PLUS   => [
                'url'     => 'https://plusone.google.com/u/0/_/+1/fastbutton?count=true&url=%s',
                'enabled' => self::NETWORK_ENABLED,
                'parse' => function($response) {
                    if (preg_match('#window\.__SSR\s*=\s*\{c:\s*(\d+)#i', $response, $matches)) {
                        return intval($matches[1]);
                    }
                    return 0;
                },
            ],
            self::NETWORK_PINTEREST     => [
                'url'     => 'http://api.pinterest.com/v1/urls/count.json?callback=pins&url=%s',
                'enabled' => self::NETWORK_ENABLED,
                'parse' => function($response) {
                    $response = preg_replace('#^\w+\(([^\)]+)\)#', '$1', $response);
                    return @json_decode($response)->count ?: 0;
                },
            ],
            self::NETWORK_LINKEDIN      => [
                'url'     => 'http://www.linkedin.com/countserv/count/share?url=%s&format=json',
                'enabled' => self::NETWORK_ENABLED,
                'parse' => function($response) {
                    return @json_decode($response)->count ?: 0;
                },
            ],
            self::NETWORK_STUMBLEUPON   => [
                'url'     => 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=%s',
                'enabled' => self::NETWORK_ENABLED,
                'parse' => function($response) {
                    $resObj = json_decode($response);

                    if ($resObj->success && $resObj->result->in_index) {
                        return $resObj->result->views;
                    }
                    return 0;
                },
            ],
            // some disabled networks
            self::NETWORK_VK            => [
                'url'     => 'http://vk.com/share.php?act=count&url=%s',
                'enabled' => self::NETWORK_DISABLED,
                'timeout' => 10000, // custom network timeout
                'parse' => function($response) {
                    if (preg_match('#VK\.Share\.count\([^,]+,\s*(\d+)\)#i', $response, $matches)) {
                        return intval($matches[1]);
                    }
                    return 0;
                },
            ],
            self::NETWORK_ODNOKLASSNIKI => [
                'url'     => 'http://www.odnoklassniki.ru/dk?st.cmd=extLike&uid=odklcnt0&ref=%s',
                'enabled' => self::NETWORK_DISABLED,
                'parse' => function($response) {
                    if (preg_match('#ODKL\.updateCount\(\'[^\']+\'\s*,\s*\'(\d+)\'\)#i', $response, $matches)) {
                        return intval($matches[1]);
                    }
                    return 0;
                },
            ],
            self::NETWORK_WYKOP         => [
                'url'     => 'http://www.wykop.pl/dataprovider/diggerwidget/?url=%s',
                'enabled' => self::NETWORK_DISABLED,
                'parse' => function($response) {
                    if (preg_match('#class=.+wykop-vote-counter[^>]+><a[^>]+>(\d+)#i', $response, $matches)) {
                        return intval($matches[1]);
                    }
                    return 0;
                },
            ],
        ];
    }

    /**
     * Returns list of available networks
     *
     * @param bool $enabledOnly (optional) Weather return only networks enabled by default
     * @return array
     */
    public function getAvailableNetworks($enabledOnly = false)
    {
        if ($enabledOnly === true) {
            return array_keys(array_filter($this->_config, function($var){
                return $var['enabled'] === self::NETWORK_ENABLED;
            }));
        }

        return array_keys($this->_config);
    }

    /**
     * Returns config for all (or provided) networks
     *
     * @param array $chosenNetworks (optional) Networks list
     * @return array
     */
    public function getNetworkConfigs(array $chosenNetworks = [])
    {
        $outNetworks = [];

        // user choose some networks
        if (!empty($chosenNetworks)) {

            foreach ($chosenNetworks as $_network) {
                if (isset($this->_config[$_network])) {
                    $outNetworks[$_network] = $this->_config[$_network];
                }
            }

        // otherwise return only enabled networks
        } else {

            foreach ($this->_config as $_network => $_config) {
                if ($_config['enabled'] == self::NETWORK_ENABLED) {
                    $outNetworks[$_network] = $_config;
                }
            }
        }

        return $outNetworks;
    }
}