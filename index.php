<?php
use Soleo\UrlShortener;
use Soleo\UrlShortener\MongoConnection;
use Soleo\UrlShortener\Shorty;

require "vendor/autoload.php";

$mongoURI = "mongodb://demo:demodemo@ds045757.mlab.com:45757/url_shortener";
$mongoConn = new MongoConnection($mongoURI);
$shorty = new Shorty($mongoConn);
if (isset($_GET['longurl'])) {
    echo $shorty->getShortUrl($_GET['longurl']);
    exit;
}
// Get Long URL
$slug = preg_replace('[^A-Za-z0-9]', '', $_SERVER['REQUEST_URI']);
$longURL = $shorty->getLongUrl($slug, true);
header("Location: ".$longURL, true, 302);
exit;
