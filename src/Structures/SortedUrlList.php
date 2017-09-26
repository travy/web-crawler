<?php
namespace WebCrawler\Structures;

use WebCrawler\Structures\Interfaces\Container;

use Iterator;

/**
 * SortedUrlList
 *
 * A sorted list of Url's which stay traversable in the order in which they
 * have been added to the list.
 *
 * TODO- ensure that list is always sorted while keeping a reference to the
 *       next URL in the sequence to ensure that no duplicate records ever
 *       exists in the ever-expanding web of URL addresses
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 25, 2017
 */

class SortedUrlList implements Container, Iterator
{
    private $list;
    private $index;
    private $maxElements;
    
    /**
     * Constructs the list and places a restriction on the maximum number of
     * URL's that are allowed to be added.
     *
     * @param int $maxElements Positive limit of elements or negative for
     * unlimited entries (until no memory remains)
     *
     * @return void
     */
    public function __construct($maxElements = -1)
    {
        $this->list = [];
        $this->index = 0;
        $this->maxElements = $maxElements;
    }
    
    /**
     * Determines if the list has been filled up to capacity.
     *
     * @return Boolean
     */
    public function isFull()
    {
        return $this->maxElements > 0 && $this->count() >= $this->maxElements;
    }

    /**
     * Adds a new unique URL to the list.
     * 
     * @param string $url
     *
     * @return Boolean
     */
    public function add($url)
    {
        //  TODO- keep the list sorted and add a reference to the inserted data
        //        to the previous element in the list
        $canAdd = !in_array($url, $this->list) && !$this->isFull();
        if ($canAdd) {
            array_push($this->list, $url);
        }
        
        return $canAdd;
    }
    
    /**
     * Adds a collection of URL's to the list.
     *
     * @param array $array
     *
     * @return array|boolean <em>true</em> if all elements in the array were
     * added successfully, or an array containing all elements which could not
     * be added.
     */
    public function addFromArray(array $array)
    {
        $invalid = [];
        foreach($array as $element) {
            $wasAdded = $this->add($element);
            if (!$wasAdded) {
                array_push($invalid, $element);
            }
        }
        
        return count($invalid) <= 0 ? true : $invalid;
    }

    /**
     * Determines if the URL exists within the list.
     *
     * @param string $url
     *
     * @return Boolean
     */
    public function contains($url)
    {
        return in_array($url, $this->list);
    }

    /**
     * Retrieves the number of elements in the list.
     *
     * @return int
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * Determines if the collection is empty.
     *
     * @return Boolean
     */
    public function isEmpty()
    {
        return $this->count() <= 0;
    }

    /**
     * Removes a URL from the list.
     *
     * @param string $url
     *
     * @return boolean
     */
    public function remove($url)
    {
        //  TODO- Once the URL's are kept sorted, this will need to ensure that
        //  the previous element points to the url which was added after the
        //  element just removeds
        $wasRemoved = false;
        foreach($this->list as $index => $item) {
            if ($url === $item) {
                array_splice($this->list, $index, 1);
                $wasRemoved = true;
                break;
            }
        }
        
        return $wasRemoved;
    }

    /**
     * Retrieves the currently selected URL
     *
     * @return string
     */
    public function current()
    {
        return $this->list[$this->index];
    }

    /**
     * Retrieves the key for the currently selected URL.
     *
     * @return string
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Points to the next URL in the sequence.
     *
     * @return void
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * Rewinds back to the first element added to the list.
     *
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Determines if the current position contains a valid URL.
     *
     * @return Boolean
     */
    public function valid()
    {
        $numElements = $this->count();
        
        return $this->index < $numElements && $numElements > 0;
    }
}
