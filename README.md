# Replace keywords in articles with links 替换文章中关键词为链接

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chuoke/keyword-linkify.svg?style=flat-square)](https://packagist.org/packages/chuoke/keyword-linkify)
[![Tests](https://github.com/chuoke/keyword-linkify/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/chuoke/keyword-linkify/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/chuoke/keyword-linkify.svg?style=flat-square)](https://packagist.org/packages/chuoke/keyword-linkify)

## Installation

You can install the package via composer:

```bash
composer require chuoke/keyword-linkify
```

## Usage

```php
$text = '<img class="hero-logo" src="/images/logos/php-logo-white.svg" alt="php" width="240" height="120">
    <p class="hero-text">A <strong>popular general-purpose scripting language</strong> that is especially suited to web development.<br>Fast, flexible and pragmatic, PHP powers everything from your blog to the most popular websites in the world.</p>';

$keywords = [
    [
        'keyword' => 'php',
        'url' => 'https://www.php.net/',
    ],
];

$keywordLinkify = new Chuoke\KeywordLinkify();
echo $keywordLinkify->replace($text, $keywords);

```

The result:

```html
<img class="hero-logo" src="/images/logos/php-logo-white.svg" alt="php" width="240" height="120"><p class="hero-text">A <strong>popular general-purpose scripting language</strong> that is especially suited to web development.<br>Fast, flexible and pragmatic, <a target="blank" href="https://www.php.net/" title="PHP">PHP</a> powers everything from your blog to the most popular websites in the world.</p>
```
[](./imgs/example-1.phg)

More example see tests.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
