<?php
namespace WebCrawler\Structures\WebAnalyzers;

use PHPHtmlParser\Dom;

use WebCrawler\Structures\Interfaces\Container;

use InvalidArgumentException;
use Iterator;

/**
 * AnalyzerRegistry
 *
 * Collection of Analyzers which can be supplied to a Crawler object.  Each
 * Analyzer will be called every time that a web page has been reached to
 * perform processing of its HTML.
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 26, 2017
 */

class AnalyzerRegistry implements Container, Iterator
{
    private $analyzers;
    private $index;
    
    public function __construct()
    {
        $this->analyzers = [];
        $this->index = 0;
    }
    
    /**
     * Executes each analyzer in the registry on a specified URL.
     *
     * @param string $url
     * @param Dom $parser
     *
     * @return void
     */
    public function execute($url, Dom $parser)
    {
        foreach ($this->analyzers as $analyzer) {
            call_user_func($analyzer, $url, $parser);
        }
    }
    
    /**
     * Inserts a new <em>AbstractAnalyzer</em> object into the registry which
     * is identified by an optionally specified name.
     *
     * @param AbstractAnalyzer $analyzer
     * @param mixed $name
     * @param boolean $allowOverwrites
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function add($analyzer, $name = null, $allowOverwrites = false)
    {
        if (!($analyzer instanceof AbstractAnalyzer)) {
            throw new InvalidArgumentException('The supplied analyzer is invalid');
        }
        
        $wasAdded = true;
        
        //  adds the analyzer if it is valid
        if (!isset($name)) {
            array_push($this->analyzers, $analyzer);
        } else if (!array_key_exists($name, $this->analyzers) || $allowOverwrites) {
            $this->analyzers[$name] = $analyzer;
        } else {
            //  the specified key for the analyzer is invalid
            $wasAdded = false;
        }
        
        return $wasAdded;
    }

    /**
     * Determines if the specified item is contained within the registry.
     *
     * @param mixed $item Either the <em>AbstractAnalyzer</em> object which
     * may have been added or its identifying key name.
     *
     * @return boolean
     */
    public function contains($item)
    {
        return $this->getKeyFor($item) !== false;
    }

    /**
     * Retrieves number of Analyzers.
     *
     * @return int
     */
    public function count()
    {
        return count($this->analyzers);
    }

    /**
     * Determines if the Registry is empty.
     *
     * @return void
     */
    public function isEmpty()
    {
        return $this->count() <= 0;
    }

    /**
     * Removes a given item from the registry.
     *
     * @param mixed $item
     *
     * @return boolean
     */
    public function remove($item)
    {
        $key = $this->getKeyFor($item);
        if ($key === false) {
            return false;
        }
        
        $index = array_search($key, array_keys($this->analyzers));
        $extracted = array_splice($this->analyzers, $index, 1);
        
        return in_array($key, array_keys($extracted));
    }
    
    /**
     * Determines the key of the given item.
     *
     * @param mixed $item An added <em>Analyzer</em> or the referencing key.
     *
     * @return mixed The key for the given item if it exists in the registry,
     * or <em>false</em> if the item does not exists
     */
    private function getKeyFor($item)
    {
        $key = false;
        
        if ($item instanceof AbstractAnalyzer) {
            $key = array_search($item, $this->analyzers, true);
        } else if (array_key_exists ($item, $this->analyzers)) {
            $key = $item;
        }
        
        return $key;
    }

    public function current()
    {
        return $this->analyzers[$this->key()];
    }

    public function key()
    {
        return array_keys($this->analyzers)[$this->index];
    }

    public function next()
    {
        ++$this->index;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function valid()
    {
        return $this->index < $this->count() && !$this->isEmpty();
    }
}
