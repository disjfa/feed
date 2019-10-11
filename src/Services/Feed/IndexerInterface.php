<?php

namespace App\Services\Feed;

use App\Entity\Feed;
use App\Entity\Item;
use App\Entity\Origin;
use DOMElement;
use DOMNode;

interface IndexerInterface
{
    /**
     * @param DOMNode $doc
     * @param Feed    $feed
     * @param Origin  $origin
     */
    public function index(DOMNode $doc, Feed $feed, Origin $origin);

    /**
     * @param DOMNode $element
     *
     * @return Item|void
     */
    public function getItem(DOMNode $element);

    /**
     * @param DOMElement $element
     * @param Origin     $origin
     */
    public function indexItem(DOMElement $element, Origin $origin);
}
