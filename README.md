Spiget.org PHP API Client
===========================

This library is a basic PHP implementation of the [Spiget.org API](https://spiget.org/).

## Installation

This library uses the [HTTPlug](https://github.com/php-http/httplug) HTTP client abstraction library -
 meaning you can use your favourite HTTP library with it!
 
For a quick and easy way to use this library in your project, via composer, run the following:

```bash
  composer require php-http/curl-client guzzlehttp/psr7 php-http/message gamepanelio/spiget-api
```

There are also [lots of different libraries](https://packagist.org/providers/php-http/client-implementation)
 that you can use with HTTPlug. To see how to use different libraries please
 [refer to the HTTPlug documentation](http://docs.php-http.org/en/latest/httplug/users.html).

## Usage

Simply instantiate a `new Spiget()` class, and use the methods it provides:

```php
<?php

use GamePanelio\SpigetApi\Spiget;

$spiget = new Spiget("My_cool_user_agent/1.0");

$response = $spiget->getResourceSearch(
    'search_param',
    [
        /* ... additional parameters ... */
    ]
);
```

### Return Data

Each method returns a PSR-7 Response.

```php
$response = $spiget->getResourcesList();

// To get the response data
var_dump($spiget->getResponseBodyFromJson($response));
// or
var_dump(json_decode($response->getBody(), true));


// To get the page count, etc
var_dump($response->getHeaderLine('X-Page-Count'));
```

### API Errors and Exceptions

Any response which is not successful (HTTP code <200>=300) will throw a `ApiCommunicationException`.

If you are using a library that throws PSR-7 errors for such responses (for example, Guzzle), they will be wrapped and
 you can access the PSR-7 exception via the `->getPrevious()` method.

## Naming Conventions

Each method provided by the `Spiget` class has a naming convention of the following format:

```
$spiget->[method-related-term][action]()
```

## License

This library is licensed under the MIT license. See the `LICENSE` file for more info.
