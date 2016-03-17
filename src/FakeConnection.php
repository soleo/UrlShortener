<?php

namespace Soleo\UrlShortener;

class FakeConnection implements ConnectionInterface
{
    private $fakeDB;
    public function __construct($config = null)
    {
        $this->fakeDB = [
            '99A' => 'https://example.com'
        ];
    }

    public function lookup($slug, $update)
    {
        return $this->fakeDB[$slug];
    }

    public function reverseLookup($longUrl)
    {
        $reversedArray = array_flip($this->fakeDB);
        return $reversedArray[$longUrl];
    }

    public function addNewRecord($longUrl, $slug)
    {
        return true;
    }

    public function getIncrementUid()
    {
        return 1;
    }

    public function cleanDB()
    {
    }
}
