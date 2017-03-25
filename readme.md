# Laravel Auth Checker

[![Build Status](https://travis-ci.org/404labfr/laravel-auth-checker.svg?branch=master)](https://travis-ci.org/404labfr/laravel-auth-checker) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/404labfr/laravel-auth-checker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/404labfr/laravel-auth-checker/?branch=master)

**Laravel Auth Checker** is a plugin to **collect login info** and **devices** used when an **user authenticate**. It makes it easy to **catch user authentication** from **new IP address** or **new devices**.  
 
- [Requirements](#requirements)
- [Installation](#installation)
- [Access Collected Data](#access-collected-data) 
    - [Logins](#logins) 
    - [Devices](#devices) 
- [Events](#events)
- [Tests](#tests)
- [Contributors](#contributors)


## Requirements

- Laravel >= 5.4
- PHP >= 5.6

## Installation

- Require it with Composer:
```bash
composer require lab404/laravel-auth-checker
```

- Add the service provider at the end of your `config/app.php`:
```php
'providers' => [
    // ...
    Lab404\AuthChecker\AuthCheckerServiceProvider::class,
],
```

- Add the trait `Lab404\AuthChecker\Models\HasLoginsAndDevices` to your **User** model.

- Migrate your database:
```php
php artisan db:migrate
```

## Access collected data

This library brings to you logins data and devices data for your users.

### Logins

```
// Your user model:
$logins = $user->logins;
// Output: 
[
    [
        'ip_address' => '1.2.3.4',
        'device_id' => 1, // ID of the used device
        'created_at' => '2017-03-25 11:42:00',
    ],
    // ... and more
]
```

Also, you can access to:
- The last logged at date : `$user->last_logged_at`
- The last login object : `$user->last_login`
- The last login IP : `$user->last_ip_address`
- All IP addresses : `$user->ip_addresses`

### Devices

```
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
        'language' => 'fr-fr'
    ],
    // ... and more
]
```

## Roadmap

- [x] Log user authentication
- [x] Collect IP addresses
- [x] Collect devices
- [x] Get user's login history
- [ ] Trust / Untrust devices
- [ ] Notify user when an unknow device log in

## Events

There are two events available that can be used to add features to you app:
- `LoginCreated` is fired when a user authenticate.
- `DeviceCreated` is fired when a new device is created for an user.

Each events returns two properties `$event->impersonator` and `$event->impersonated` containing User model isntance.

## Tests

```bash
vendor/bin/phpunit
```

## Contributors

- [MarceauKa](https://github.com/MarceauKa)
- and all others [contributors](https://github.com/404labfr/laravel-auth-checker/graphs/contributors)

## Licence

MIT
