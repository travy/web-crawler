<?php
namespace WebCrawler\Structures\Interfaces;

/**
 * LinkedListNode
 *
 * Provides an interface for operations performed on a LinkedListNode.
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 26, 2017
 */

interface LinkedListNode
{
    public function hasNextNode();
    public function getNextNode();
}
