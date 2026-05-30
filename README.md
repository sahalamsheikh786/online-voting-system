# Online Voting System

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

## About Project
A Laravel-based Online Voting System for secure and transparent elections.  
This project ensures:
- District-wise candidate and voter management  
- One vote per user with strict validation  
- Real-time results after polls close  
- Secure authentication for Admin and Voter  

## Deploying on Render
This repo includes a Docker-based `render.yaml` Blueprint for Render.

1. Push the project to GitHub.
2. In Render, create a new Blueprint from the repo.
3. Render creates a web service and Postgres database, then injects `DB_URL`.
4. The container runs migrations at startup and binds to Render's `$PORT`.

For a manual Render web service, use Docker and set these environment variables:

```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_URL=<your Render Postgres internal connection string>
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Set `APP_KEY` to the output of:

```bash
php artisan key:generate --show
```

On first deploy, the startup script creates a default admin if one does not exist:

```text
Contact number: 9800000000
Password: admin12345
Pattern lock: 1258
```

You can override these with `DEFAULT_ADMIN_CONTACT`, `DEFAULT_ADMIN_PASSWORD`,
and `DEFAULT_ADMIN_PATTERN` in Render.

<!-- 
================ OLD README CONTENT (Laravel default) ================
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel
Laravel is a web application framework with expressive, elegant syntax...
(etc.)
=====================================================================
-->

## License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
