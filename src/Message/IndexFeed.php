<?php

namespace App\Message;

class IndexFeed
{
    /**
     * @var string
     */
    private $feedId;

    /**
     * IndexFeed constructor.
     */
    public function __construct(string $feedId)
    {
        $this->feedId = $feedId;
    }

    public function getFeedId(): string
    {
        return $this->feedId;
    }
}
