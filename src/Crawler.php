<?php
namespace WebCrawler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\CurlException;

use WebCrawler\Structures\SortedUrlList;
use WebCrawler\Structures\WebAnalyzers\AnalyzerRegistry;

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
    private $urlQueue;
    private $registry;
    
    //  TODO- create a ConfigTrait that will automatically map custom and default configurations
    protected $_defaultConfigs = [
        'maxCrawls' => 200,
    ];
    
    /**
     * Constructs a new Crawler which will explore the web starting from a
     * given URL.
     *
     * Options:
     * 1. maxCrawls - Specifies the maximum number of web pages to crawl
     *    through
     *
     * @param string $rootUrl
     * @param array $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct($rootUrl, AnalyzerRegistry $registry = null, array $options = [])
    {
        $configs = $this->getConfigs($options);
        
        if (!$this->setRoot($rootUrl)) {
            throw new InvalidArgumentException(sprintf("The supplied URL value {%s} is invalid.", $rootUrl));
        }
        
        //  create a queue and add the rootUrl
        $this->urlQueue = new SortedUrlList($configs['maxCrawls']);
        $this->urlQueue->add($rootUrl);
        
        //  create the registry for all page analyzers
        $this->registry = is_null($registry) ?
                new AnalyzerRegistry() :
                $registry;
    }
    
    /**
     * Sets the root URL address.
     *
     * @param string $rootUrl
     *
     * @return Boolean
     */
    public function setRoot($rootUrl)
    {
        $safeUrl = self::isValidUrl($rootUrl);
        
        if ($safeUrl) {
            $this->root = $rootUrl;
        }
        
        return $safeUrl;
    }
    
    /**
     * Retrieves the URL for the first web page.
     *
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    /**
     * Will crawl through the web executing analyzers on each page that is
     * touched.
     *
     * @return void
     */
    public function crawl()
    {
        foreach ($this->urlQueue as $url) {
            $parser = $this->getParser($url);
            if ($parser !== false) {
                //  execute all urls on the given page
                $this->registry->execute($url, $parser);
                
                //  retrieve a list of all links on the page and add them to the queue
                $links = $this->getLinksOnPage($url, $parser);
                $this->urlQueue->addFromArray($links);
            }
        }
        
        echo print_r($this->urlQueue, true);
    }
    
    /**
     * Retrieves a parser for the content listed on the URL page.
     *
     * @param string $url
     *
     * @return boolean|Dom false if the content from the page could not
     * accessed or parsed, otherwise a parselable dom object.
     */
    public function getParser($url)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $url, ['verify' => false]);
            $html = $response->getBody();

            $dom = new Dom();
            return $dom->load($html);
        } catch (CurlException $ex) {
            // TODO - decide what to do on HTTP error conditions, ignoring seems useless
        } catch (ClientException $clientEx) {
            
        } catch (ConnectException $connectEx) {
            
        } catch (ServerException $serverEx) {
            
        }
        
        return false;
    }
    
    /**
     * Parses out all links found within a given web page.
     *
     * @param string $url
     *
     * @return array List of all URL addresses
     */
    public function getLinksOnPage($url, Dom $parser = null)
    {
        $results = [];
        
        //  construct a new parser if none has been provided
        if (is_null($parser)) {
            $parser = $this->getParser($url);
            if ($parser === false) {
                return false;
            }
        }
        
        //  obtain all links specified within HTML anchor tags
        $anchors = $parser->find('a');
        foreach($anchors as $anchor) {
            $href = $anchor->getAttribute('href');
            if (!is_null($href) && self::isValidUrl($href)) {
                array_push($results, $href);
            }
        }

        return $results;
    }
    
    /**
     * Retrieves the user customized configurations.
     *
     * @param array $options
     *
     * @return array
     */
    private function getConfigs(array $options = [])
    {
        return array_merge($this->_defaultConfigs, $options);
    }
    
    /**
     * Determines if the URL specified is valid or not.
     *
     * @param string $url
     *
     * @return Boolean
     */
    private static function isValidUrl($url)
    {
        if (strpos($url, '://') === false) {
            $url = 'http://' . $url;
        }
        
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
