<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Case Story

For a corporate business engaged in car maintenance and cleaning, you will be expected to
write the necessary API services for a mobile application that tracks your services with
customers. Customers will be able to log in, order, and track services that they bought over the
application.

This project development with Laravel Framework v9.25.1 and PHP 8.1.9 <br>
Database: MySQL,<br>
Cache: Redis,<br>

### Laravel/composer used packages
- [Laravel Sanctum](https://laravel.com/docs/9.x/sanctum).
- [Laravel-permission (spatie 3rd party)](https://spatie.be/docs/laravel-permission/v5/introduction).
- [Eloquent](https://laravel.com/docs/9.x/eloquent).
- [Eloquent: API Resources](https://laravel.com/docs/9.x/eloquent-resources).
- [Artisan Console](https://laravel.com/docs/9.x/artisan).
- [Laravel Telescope](https://laravel.com/docs/9.x/telescope).
- [Predis](https://github.com/predis/predis).


### First use

- composer install
- create a new '.env' file using with '.env.example'
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- php artisan optimize:clear
- php artisan serve

### Commands
- cars:sync
  - Run every hour
- order:complete
  - Run every day

#  Postman 🚀
- **[Postman Mobile User Api v1](https://documenter.getpostman.com/view/7841503/VUqvqv6G)**
- **[Postman Admin User Api v1](https://documenter.getpostman.com/view/7841503/VUqvqv6J)**

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
