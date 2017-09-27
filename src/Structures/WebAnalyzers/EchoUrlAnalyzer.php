<?php
namespace WebCrawler\Structures\WebAnalyzers;

use PHPHtmlParser\Dom;

/**
 * EchoUrlAnalyzer
 * 
 * Simple Analyzer used for echo-ing any URL's that the Web Crawlers lands on.
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 26, 2017
 */
class EchoUrlAnalyzer extends AbstractAnalyzer
{
    public function __construct() {
        
    }
    
    /**
     * Writes the URL to STDOUT.
     *
     * @param string $url
     * @param Dom $parser
     *
     * @return void
     */
    public function analyze($url, Dom $parser)
    {
        fwrite(STDOUT, sprintf("\n%s", $url));
    }
}
