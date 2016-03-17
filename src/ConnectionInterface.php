<?php

namespace Soleo\UrlShortener;

interface ConnectionInterface
{

    public function lookup($slug, $update);

    public function reverseLookup($longUrl);

    public function addNewRecord($longUrl, $slug);

    public function getIncrementUid();

    public function cleanDB();
}
