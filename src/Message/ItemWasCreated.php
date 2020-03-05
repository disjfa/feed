<?php

namespace App\Message;

class ItemWasCreated
{
    /**
     * @var string
     */
    private $itemId;

    public function __construct(string $itemId)
    {
        $this->itemId = $itemId;
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }
}
