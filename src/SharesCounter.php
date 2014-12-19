<?php
/**
 *
 * @author Dominik Bułaj <dominik@bulaj.com>
 */

namespace SharesCounter;

// check required PHP extensions
if (!\function_exists('curl_init')) {
    throw new Exception('Shares counter lib needs the CURL PHP extension.');
}

/**
 * Class ShareCounts
 * Main class used to fetch share counts
 *
 * @package ShareCounts
 */
class SharesCounter
{
    /**
     * @var string
     */
    private $_url;

    /**
     * @param string $url Resource URL
     * @throws Exception
     */
    public function __construct($url)
    {
        if (!$url) {
            throw new Exception('Resource URL is missing');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('Resource URL is invalid');
        }

        $this->_url = $url;
    }

    /**
     * @param array $networks (Optional) Array list of networks to check
     * @return array
     */
    public function getShares(array $networks = [])
    {
        $requestNetworks = (new Networks())->getNetworkConfigs($networks);
        $data = $this->requestData($requestNetworks);

        return $data;
    }

    /**
     * @param array $networks
     * @return array
     */
    protected function requestData(array $networks)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // fixes CURL Error 60 (SSL certificate problem: unable to get local issuer certificate)
        ]);

        $shares = [];
        foreach ($networks as $_network => $_config) {
            $timeout = Networks::DEFAULT_TIMEOUT;
            if (isset($_config['timeout']) && is_numeric($_config['timeout'])) {
                $timeout = $_config['timeout'];
            }
            $url = sprintf($_config['url'], $this->_url);

            curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout);
            curl_setopt($curl, CURLOPT_URL, $url);

            $data = curl_exec($curl);
            $errno = curl_errno($curl);
            $error = curl_error($curl);

            if ($errno === 0) {
                $shares[$_network] = intval($_config['parse']($data));
            } else {
                $shares[$_network] = "Error (#{$errno}): {$error}";
            }
        }

        curl_close($curl);

        return $shares;
    }
}