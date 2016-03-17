<?php

namespace Soleo\UrlShortener;

use Soleo\UrlShortener\MongoConnection;
use Soleo\UrlShortener\FakeConnection;
use Soleo\UrlShortener\Shorty;
use \Mockery as m;

class ShortyTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    protected function setUp()
    {
    }

    public function testShortURLConvertToLongURL()
    {
        $shortURL = "A9A";
        $mongoURI = "mongodb://demo:demodemo@ds045757.mlab.com:45757/url_shortener";
        $mongoConn = new MongoConnection($mongoURI);
        $shorty = new Shorty($mongoConn);
        $longURL = $shorty->getLongURL($shortURL);
        $this->assertEquals('https://example.com', $longURL);
    }

    public function testLongURLConvertToShortURL()
    {
        $longURL = "https://example.com";
        $mongoURI = "mongodb://demo:demodemo@ds045757.mlab.com:45757/url_shortener";
        $mongoConn = new MongoConnection($mongoURI);
        $shorty = new Shorty($mongoConn);
        $shortURL = $shorty->getShortUrl($longURL);
        $this->assertEquals('http://localhost/A9A', $shortURL);
    }

    public function testWithFakeConnection()
    {
        $longURL = "https://example.com";
        $fakeConn = new FakeConnection;
        $shorty = new Shorty($fakeConn);
        $shortURL = $shorty->getShortUrl($longURL);
        $this->assertEquals('http://localhost/A9A', $shortURL);
    }

    public function testGetURLsFromMockConnection()
    {
        $longURL = "https://example.com";
        $mockConn = m::mock('Soleo\UrlShortener\MongoConnection');
        $mockConn->shouldReceive('reverseLookup')->andReturn('A9A');
        $mockConn->shouldReceive('lookup')->andReturn('https://example.com');

        $shorty = new Shorty($mockConn);
        $this->assertInstanceOf(Shorty::class, $shorty);

        $shortURL = $shorty->getShortUrl($longURL);
        $this->assertEquals('http://localhost/A9A', $shortURL);

        $long = $shorty->getLongUrl('A9A');
        $this->assertEquals('https://example.com', $long);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException()
    {
        $longURL = "ht";
        $mockConn = m::mock('Soleo\UrlShortener\MongoConnection');
        $shorty = new Shorty($mockConn);
        $shorty->getShortUrl($longURL);
    }
}
