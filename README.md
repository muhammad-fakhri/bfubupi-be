# BFUBUPI-BE

## Description

This project is a backend service for official site of Bakti Formica Untuk Bangsa UPI, an annual event hosted by Biology Department Universitas Pendidikan Indonesia.

## Development Tools

-   [Lumen v8.0.1](https://lumen.laravel.com/)
-   [JSON Web Token Authentication for Laravel & Lumen](https://github.com/tymondesigns/jwt-auth)
-   [Faker](https://github.com/fzaninotto/Faker)
-   [Lumen Generator](https://github.com/flipboxstudio/lumen-generator)

## Set Up

1. Clone this repo and change directory to project folder  
   `git clone https://github.com/muhammad-fakhri/bfubupi-be.git && cd /bfubupi-be`
2. Install dependencies  
   `composer install`
3. Copy .env.example to .env  
   `cp .env.example .env`
4. Generate aplication key  
   `php artisan key:generate`
5. Generate JWT secret key  
   `php artisan jwt:secret`
6. Set your database credential in .env on key DB\_\*
7. Set your mail credential in .env on key MAIL\_\*
8. If you are in production, do not forget to set APP_ENV in .env to "production" and set APP_DEBUG to "false"

## Developer Team

-   Muhammad Fakhri ([@muhammad-fakhri](https://github.com/muhammad-fakhri))
