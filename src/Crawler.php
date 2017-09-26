<?php
namespace WebCrawler;

use InvalidArgumentException;

/**
 * Crawler
 *
 * Will iterate through the web starting from some root web location and
 * executes a collection of Analyzers on each page.
 *
 * The duration in which the Crawler will continue working for is based on
 * a set of configurations that can be specified through the constructor.
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 25, 2017
 */

class Crawler
{
    private $root;
    
    protected $_defaultConfigs = [
        'maxCrawls' => -1,
    ];
    
    public function __construct($rootUrl)
    {
        if (!$this->setRoot($rootUrl)) {
            throw new InvalidArgumentException(sprintf("The supplied URL value {%s} is invalid.", $rootUrl));
        }
    }
    
    public function setRoot($rootUrl)
    {
        $safeUrl = self::isValidUrl($rootUrl);
        
        if ($safeUrl) {
            $this->root = $rootUrl;
        }
        
        return $safeUrl;
    }
    
    public function getRoot()
    {
        return $this->root;
    }
    
    public function crawl(array $options = [])
    {
        $configs = $this->getConfigs($options);
        
        echo file_get_contents($this->root);
    }
    
    private function getConfigs(array $options = [])
    {
        return array_merge($this->_defaultConfigs, $options);
    }
    
    private static function isValidUrl($url)
    {
        if (strpos($url, '://') === false) {
            $url = 'http://' . $url;
        }
        
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
