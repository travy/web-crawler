<?php
namespace WebCrawler\Structures\Interfaces;

use Countable;

/**
 * Container
 *
 * Defines an interface for any Container of data used within the Web Crawler
 * design.
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 25, 2017
 */

interface Container extends Countable
{
    public function add($item);
    public function remove($item);
    public function isEmpty();
    public function contains($item);
}
