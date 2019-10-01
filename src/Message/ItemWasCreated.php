<?php

namespace App\Message;

class ItemWasCreated
{
    /**
     * @var string
     */
    private $itemId;

    /**
     * @param string $itemId
     */
    public function __construct(string $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return string
     */
    public function getItemId(): string
    {
        return $this->itemId;
    }
}
