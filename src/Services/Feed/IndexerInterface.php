<?php

namespace App\Services\Feed;

use App\Entity\Feed;
use App\Entity\Item;
use App\Entity\Origin;
use DOMElement;
use DOMNode;

interface IndexerInterface
{
    public function index(DOMNode $doc, Feed $feed, Origin $origin);

    /**
     * @return Item|void
     */
    public function getItem(DOMNode $element);

    public function indexItem(DOMElement $element, Origin $origin);
}
