# Changelog

## 1.6.2

- Laravel 9.x support [#23](https://github.com/404labfr/laravel-auth-checker/pull/23)

## 1.6.1

- Laravel 8.x support

## 1.6.0

- Laravel 7.x support

## 1.5.2

- Update polymorphic relation [#18](https://github.com/404labfr/laravel-auth-checker/pull/18) 

## 1.5.1

⚠️ Run `php artisan vendor:publish --tag=auth-checker` 

- Fixed database new polymorphic column

## 1.5.0

- Users now use MorphMany instead of HasMany
- Devices and Logins now use MorphTo instead of BelongsTo
- Tests updated with PHP 7.4

## 1.4.2

- Fix config publishing
- Fix migrations: use bigIncrements and bigInteger, disable foreign references

## 1.4.1

- Package auto-discover
- Fix tests

## 1.4.0

- Changed how the application boot

## 1.3.0

- Laravel 6.0
- Allow model customization
- PHPDoc and typed return values

## 1.2.0

- BC : Laravel 5.8 requirements

## 1.1.1

- Fixed : empty device model in DeviceCreated event [PR#8](https://github.com/404labfr/laravel-auth-checker/pull/8)