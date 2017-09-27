<?php
namespace WebCrawler\Structures\Interfaces;

/**
 * BinaryTreeNode
 *
 * Provides an interface used by a node in a Binary tree.
 *
 * @author Travis Anthony Torres <travis@travistorres.com>
 * @version September 26, 2017
 */

interface BinaryTreeNode
{
    public function hasLeftNode();
    public function hasRightNode();
    public function getLeftNode();
    public function getRightNode();
}
