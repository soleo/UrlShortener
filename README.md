# UrlShortener

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![StyleCI][ico-styleci]][link-styleci]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A Simple, Self-Hosted URL Shortener with MongoDB. 

## Install

Via Composer

``` bash
$ composer require soleo/url-shortener
```

## Usage

``` php
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
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email shaoxinjiang@gmail.com instead of using the issue tracker.

## Credits

- [Xinjiang Shao][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/soleo/url-shortener.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/soleo/url-shortener/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/soleo/url-shortener.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/soleo/url-shortener.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/soleo/url-shortener.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/32484055/shield

[link-packagist]: https://packagist.org/packages/soleo/url-shortener
[link-travis]: https://travis-ci.org/soleo/url-shortener
[link-scrutinizer]: https://scrutinizer-ci.com/g/soleo/url-shortener/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/soleo/url-shortener
[link-downloads]: https://packagist.org/packages/soleo/url-shortener
[link-author]: https://github.com/soleo
[link-contributors]: ../../contributors
[link-styleci]: https://styleci.io/repos/32484055
