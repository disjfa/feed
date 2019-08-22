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
     *
     * @param string $feedId
     */
    public function __construct(string $feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * @return string
     */
    public function getFeedId(): string
    {
        return $this->feedId;
    }
}
