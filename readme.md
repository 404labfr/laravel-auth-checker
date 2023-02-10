# Laravel Auth Checker

[![Build Status](https://travis-ci.org/404labfr/laravel-auth-checker.svg?branch=master)](https://travis-ci.org/404labfr/laravel-auth-checker) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/404labfr/laravel-auth-checker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/404labfr/laravel-auth-checker/?branch=master)

**Laravel Auth Checker** is a plugin to **collect login info** and **devices** used when a **user authenticates**. It makes it easy to **catch user authentication attempts and lockouts** from **new IP address** or **new devices**.
  
![Example logins table](screenshot.png?raw=true)
 
- [Requirements](#requirements)
- [Installation](#installation)
- [Access Collected Data](#access-collected-data) 
    - [Logins](#logins) 
    - [Devices](#devices) 
- [Events](#events)
- [Practical usage](#practical-usage)
- [Tests](#tests)
- [Contributors](#contributors)


## Requirements

- Laravel 9 to 10
- PHP 8.0 to 8.2

### Laravel support

| Version  | Release |
|:--------:|:-------:|
|  9, 10   |   2.0   |
|   8, 9   |   1.7   |
|   6, 7   |   1.6   |
|   5.8    |   1.2   |
| 5.7, 5.6 |   1.1   |

## Installation

- Require it with Composer:

```bash
composer require lab404/laravel-auth-checker
```

- Add to your **User** model the `Lab404\AuthChecker\Models\HasLoginsAndDevices` trait and the `Lab404\AuthChecker\Interfaces\HasLoginsAndDevicesInterface` interface.

```php
use Lab404\AuthChecker\Models\HasLoginsAndDevices;
use Lab404\AuthChecker\Interfaces\HasLoginsAndDevicesInterface;

class User extends Authenticatable implements HasLoginsAndDevicesInterface
{
    use Notifiable, HasLoginsAndDevices;  
}
```

- Publish migrations and migrate your database:

```bash
php artisan vendor:publish --tag=auth-checker
php artisan migrate
```

Note: Migrations are published in case you need to customize migration timestamps to integrate to your existing project.

## Access collected data

This library collects login data and devices data about your users.

### Logins

```php
// Your user model:
$logins = $user->logins;
// Output: 
[
    [
        'ip_address' => '1.2.3.4',
        'device_id' => 1, // ID of the used device
        'type' => 'auth',
        'device' => [
            // See Devices
        ],
        'created_at' => '2017-03-25 11:42:00',
    ],
    // ... and more
]
```

Also, you can directly access logins by their type:
- `$user->auths`, returns successful logins (via `Login::TYPE_LOGIN`)
- `$user->fails`, returns failed logins (via `Login::TYPE_FAILED`)
- `$user->lockouts`, returns locked out logins (via `Login::TYPE_LOCKOUT`)

### Devices

```php
// Your user model:
$devices = $user->devices;
// Outputs:
[
    [
        'platform' => 'OS X',
        'platform_version' => '10_12_2',
        'browser' => 'Chrome',
        'browser_version' => '54',
        'is_desktop' => true,
        'is_mobile' => false,
        'language' => 'fr-fr',
        'login' => [
          // See logins
        ],
    ],
    // ... and more
]
```

## Roadmap

- [x] Log user authentication
- [x] Collect IP addresses
- [x] Collect devices
- [x] Get user's login history
- [x] Get devices history
- [x] Capture failed logins
- [x] Capture lockout logins
- [ ] Trust / Untrust devices
- [ ] Notify user when an unknown device log in

## Events

There are many events available that can be used to add features to your app:
- `LoginCreated` is fired when a user authenticates.
- `DeviceCreated` is fired when a new device is created for a user.
- `FailedAuth` is fired when a user fails to log in.
- `LockoutAuth` is fired when authentication is locked for a user (too many attempts).

Each event passes a `Login` model and a `Device` model to your listeners.

## Practical usage

Once the trait `HasLoginsAndDevices` is added to your `User` model, it is extended with these methods:

- `logins()` returns all logins
- `auths()` returns all successful login attemps
- `fails()` returns all failed login attempts
- `lockouts()` returns all lockouts

Each login returned is associated with the `Device` model used.

- `devices()` returns all devices used by the user to authenticate.

## Tests

```bash
vendor/bin/phpunit
```

## Contributors

- [MarceauKa](https://github.com/MarceauKa)
- and all others [contributors](https://github.com/404labfr/laravel-auth-checker/graphs/contributors)

## Licence

MIT
