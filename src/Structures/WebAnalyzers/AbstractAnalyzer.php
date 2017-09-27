<?php
namespace WebCrawler\Structures\WebAnalyzers;

use PHPHtmlParser\Dom;

/**
 * AbstractAnalyzer
 *
 * Analyzers are pieces of logic which will be performed on each web page that
 * a Crawler object accesses.  Such operations may read the HTML DOM looking 
 * for specific patterns or content in hopes of building a search engine, or
 * could look at the various technologies such as Bootstrap, jQuery, ember.js
 * that web-pages utilize to keep track of what technologies are trending.
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 26, 2017
 */

abstract class AbstractAnalyzer
{
    /**
     * Operation called to analyze the current DOM page.
     *
     * @return void
     */
    public abstract function analyze($url, Dom $parser);
    
    /**
     * Allow the Analyzer to be invokable.
     *
     * @param string $url
     * @param Dom $parser
     *
     * @return void
     */
    public function __invoke($url, Dom $parser)
    {
        $this->analyze($url, $parser);
    }
}
