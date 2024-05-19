# Laravel Auth Checker

[![Build Status](https://travis-ci.org/404labfr/laravel-auth-checker.svg?branch=master)](https://travis-ci.org/404labfr/laravel-auth-checker) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/404labfr/laravel-auth-checker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/404labfr/laravel-auth-checker/?branch=master)

**Laravel Auth Checker** is a plugin to **collect login info** and **devices** used when a **user authenticates**. It makes it easy to **catch user authentication attempts and lockouts** from **new IP address** or **new devices**.
  
![Example logins table](screenshot.png?raw=true)
 
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
  - [Authenticatable model](#authenticatable-model)
  - [Logins](#logins)
  - [Devices](#devices)
  - [Events](#events)
- [Tests](#tests)
- [Contributors](#contributors)
- [Licence](#licence)


### Requirements

| Version  |                              Release                               |
|:--------:|:------------------------------------------------------------------:|
|    11    | [3.0](https://github.com/404labfr/laravel-auth-checker/tree/3.0.0) |
|  9, 10   | [2.0](https://github.com/404labfr/laravel-auth-checker/tree/2.0.0) |
|   8, 9   | [1.7](https://github.com/404labfr/laravel-auth-checker/tree/1.7.0) |
|   6, 7   | [1.6](https://github.com/404labfr/laravel-auth-checker/tree/1.6.2) |

## Installation

* Require the package: `composer require lab404/laravel-auth-checker`
* Publish migration files: `php artisan vendor:publish --tag=auth-checker`
* Migrate your database: `php artisan migrate`
* Configure your `Authenticatable` model (see below)

## Usage

This library collects login data and devices data about your users.

### Authenticatable model

Your `Authenticatable` model (usually `User`) must implement the `HasLoginsAndDevicesInterface` interface.

The trait `HasLoginsAndDevices` is provided with for a working default implementation.

```php
use Lab404\AuthChecker\Models\HasLoginsAndDevices;
use Lab404\AuthChecker\Interfaces\HasLoginsAndDevicesInterface;

class User extends Authenticatable implements HasLoginsAndDevicesInterface
{
    // ...
    use HasLoginsAndDevices;  
}
```

Once configured, you can access the following methods

- `logins()` returns all logins
- `auths()` returns all successful login attemps
- `fails()` returns all failed login attempts
- `lockouts()` returns all lockouts

Each login returned is associated with the `Device` model used

- `devices()` returns all devices used by the user to authenticate.

### Logins

Calling `$user->logins` outputs:

```php
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
    // ...
]
```

Also, you can directly access logins by their type

- `$user->auths`, returns successful logins (via `Login::TYPE_LOGIN`)
- `$user->fails`, returns failed logins (via `Login::TYPE_FAILED`)
- `$user->lockouts`, returns locked out logins (via `Login::TYPE_LOCKOUT`)

### Devices

Calling `$user->devices` outputs:

```php
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
    // ...
]
```

### Events

There are many events available that can be used to add features to your app

- `LoginCreated` is fired when a user authenticates.
- `DeviceCreated` is fired when a new device is created for a user.
- `FailedAuth` is fired when a user fails to log in.
- `LockoutAuth` is fired when authentication is locked for a user (too many attempts).

Each event passes a `Login` model and a `Device` model to your listeners.

## Tests

```bash
vendor/bin/phpunit
```

## Contributors

- [MarceauKa](https://github.com/MarceauKa)
- and all others [contributors](https://github.com/404labfr/laravel-auth-checker/graphs/contributors)

## Licence

MIT
