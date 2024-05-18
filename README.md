# kanyjoz/sessions
Session handler with PSR-15 middleware

## Requirements
* PHP 8.3+

## Installation
```
composer require kanyjoz/sessions
```

## Description
Cookie-based session handler that you can easily slot in your Slim 4+ projects with the PSR-15 middleware.
This is a pet project, exercise form that is mostly based https://github.com/odan/session, so most credit goes to him.
I wanted to create something that I can use to learn code and can be in a public github repository, so others can point out my mistakes and I can learn about open-source contributions.

## Slim 4 integration
```php
<?php

use DI\Container;
use Slim\Factory\AppFactory;
use KanyJoz\Sessions\Session;
use KanyJoz\Sessions\SessionConfig;
use KanyJoz\Sessions\Interface\SessionInterface;
use KanyJoz\Sessions\Interface\SessionManagerInterface;
use KanyJoz\Sessions\Interface\FlashInterface;
use KanyJoz\Sessions\Middleware\SessionStartMiddleware;


// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);

// Configure DI container using SessionConfig object
$container->set(Session::class, function() {
    return new Session(new SessionConfig());
});

// Also register 3 interfaces that you use to interact with your session
$container->set(SessionManagerInterface::class, function(Session $session) {
    return $session; // to start, stop your session
});
$container->set(SessionInterface::class, function(Session $session) {
    return $session; // to put, get session values
});
$container->set(FlashInterface::class, function(Session $session) {
    return $session; // to put, get flash values
});

// Create the application
$app = AppFactory::create();

// Register the middleware to automatically start and stop your session on each request
$app->add(new SessionStartMiddleware());

// Handle incoming HTTP requests
$app->run();
```

## SessionConfig
The following values are used and passed down to configure the session and the session cookie.

```php
<?php

// Session Cookie Config
private int $lifetime = 3600; // 1 hr
private string $path = '/';
private string $domain = '';
private bool $secure = false; // Should be true for production with HTTPS connection
private bool $httponly = true;
private string $samesite = 'Lax'; // Should be Strict in production

// Session Config
private string $name = 'PHPSESSID';
private int $sid_length = 32; // Should be 96 in production
private int $sid_bits_per_character = 4; // Should be 6 in production
private bool $use_strict_mode = false; // Should be true in production
private string $cache_limiter = 'nocache';
private string $referer_check = ''; // Should be set if domain is set
```

If you are in production or want to change some of the defaults: each of them have getters and fluid setters.

```php
<?php

use KanyJoz\Sessions\Session;
use KanyJoz\Sessions\SessionConfig;

//...

$container->set(SessionConfig::class function() {
    return (new SessionConfig())
        ->setSamesite('Strict')
        ->setSecure(true)
        ->setSidLength(96)
        ->setSidBitsPerCharacter(6);
});

$container->set(Session::class, function(SessionConfig $config) {
    return new Session($config);
});
```

## License
MIT