<?php

namespace Soleo\UrlShortener;

use Soleo\UrlShortener\MongoConnection;
use Soleo\UrlShortener\FakeConnection;
use Soleo\UrlShortener\Shorty;
use \Mockery as m;

class ShortyTest extends \PHPUnit_Framework_TestCase
{
    private $conn;

    protected function tearDown()
    {
        m::close();
    }

    protected function setUp()
    {
        $mongoURI = "mongodb://demo:demodemo@ds045757.mlab.com:45757/url_shortener";
        $mongoConn = new MongoConnection($mongoURI);
        $this->conn = $mongoConn;
    }

    public function testLongURLConvertToShortURL()
    {
        $longURL = "https://example.com";
        $shorty = new Shorty($this->conn);
        $shortURL = $shorty->getShortUrl($longURL);
        $this->assertEquals('http://localhost/99A', $shortURL);
    }

    public function testShortURLConvertToLongURL()
    {
        $shortURL = "99A";
        $shorty = new Shorty($this->conn);
        $longURL = $shorty->getLongURL($shortURL);
        $this->assertEquals('https://example.com', $longURL);
    }

    public function testWithFakeConnection()
    {
        $longURL = "https://example.com";
        $fakeConn = new FakeConnection;
        $shorty = new Shorty($fakeConn);
        $shortURL = $shorty->getShortUrl($longURL);
        $this->assertEquals('http://localhost/99A', $shortURL);
    }

    public function testGetURLsFromMockConnection()
    {
        $longURL = "https://example.com";
        $mockConn = m::mock('Soleo\UrlShortener\MongoConnection');
        $mockConn->shouldReceive('reverseLookup')->andReturn('99A');
        $mockConn->shouldReceive('lookup')->andReturn('https://example.com');

        $shorty = new Shorty($mockConn);
        $this->assertInstanceOf(Shorty::class, $shorty);

        $shortURL = $shorty->getShortUrl($longURL);
        $this->assertEquals('http://localhost/99A', $shortURL);

        $long = $shorty->getLongUrl('99A');
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
