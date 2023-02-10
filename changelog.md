# Changelog

## 2.0.0

- Bump minimal Laravel version to 8.x
- Bump minimal PHP version to 8.x
- Fix N+1 [#30](https://github.com/404labfr/laravel-auth-checker/pull/30), thanks to [michael-rubel](https://github.com/michael-rubel)

## 1.7.0

- Bump minimal Laravel version to 8.x
- Bump minimal PHP version to 7.3
- Fix lazy loading [#25](https://github.com/404labfr/laravel-auth-checker/pull/25), thanks to [michael-rubel](https://github.com/michael-rubel)
- Ability to skip logging [#21](https://github.com/404labfr/laravel-auth-checker/pull/21), thanks to [danielboendergaard](https://github.com/danielboendergaard)
- Add chinese language files [#29](https://github.com/404labfr/laravel-auth-checker/pull/29), thanks to [guanguans](https://github.com/guanguans)

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
