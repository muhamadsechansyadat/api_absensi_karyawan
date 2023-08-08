<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Installation

- Clone aplikasi dari repository `git clone https://github.com/muhamadsechansyadat/api_absensi_karyawan.git`
- Lakukan command `composer install`
- Membuat database dengan nama bebas di database anda
- Membuat file `.env` dan copy isinya dari `.env.example`
- Konekan `.env` dengan database anda
- Jalankan command `php artisan key:generate`
- Jalankan command `php artisan jwt:secret`
- Jalankan command `php artisan migrate`
- Jalankan command `php artisan serve`

## Terintegrasi Dengan

- **[stevebauman/location](https://github.com/stevebauman/location)**
- **[DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)**
- **[tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)**
