<img width="2880" height="6886" alt="FireShot Capture 005 - PayrollSystem - localhost" src="https://github.com/user-attachments/assets/89442e9b-6a38-4288-b34d-cbdb0c8b98c2" />

<img width="2880" height="4027" alt="FireShot Capture 006 - PayrollSystem - localhost" src="https://github.com/user-attachments/assets/532084d8-1721-46ff-a756-d73205e39a14" />


<img width="2880" height="3982" alt="PayrollSystem(1)" src="https://github.com/user-attachments/assets/4d2196e6-a8dd-453f-9f7e-6ee7b6db5b61" />

<img width="2880" height="3950" alt="PayrollSystem" src="https://github.com/user-attachments/assets/c3a9f864-535a-40b7-9c49-e1ca1282f985" />


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



###### Notes 
```
php artisan db:seed --class=EmployeeSeeder
```

##### For shared hosting
edit bootstrap/app : 
```
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role' => App\Http\Middleware\EnsureUserRole::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

    For link 
     <!-- Scripts -->
    <link rel="stylesheet" href="/build/assets/app-BmzOR7Uu.css">
<link rel="stylesheet" href="/build/assets/app-CEDkEsNM.css">
<link rel="stylesheet" href="/build/assets/app-CXmy2yOf.css">
<script type="module" src="/build/assets/app--NsOB_Ad.js"></script>


FileSystem COnfig : 
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

```

```
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM payrolls;
DELETE FROM employees;
SET FOREIGN_KEY_CHECKS = 1;

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM users
WHERE name != 'Administrator';
SET FOREIGN_KEY_CHECKS = 1;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE payrolls;
SET FOREIGN_KEY_CHECKS = 1;
```


##### Update status email di table payrolls :
```
UPDATE payrolls p
JOIN employees e ON e.id = p.employee_id
SET
    p.email_status = 'pending',
    p.email_error = NULL,
    p.email_sent_at = NULL
WHERE e.email IN (
    'rizalsuherlin07@gmail.com',
    'dikibudi321@gmail.com',
    'fitriprt94@gmail.com',
    'kaniunsrafri12612@gmail.com',
    'suryamaya07@gmail.com',
    'adisobari18@gmail.com',
    'diankharani29@icloud.com',
    'fauzanmufadli7@gmail.com',
    'totighandi.tigan@gmail.com',
    'haditlawan1@gmail.com',
    'zmayulianf55@gmail.com',
    'ryanfirmansyah71322@gmail.com',
    'diarnibi64@gmail.com',
    'zhrys10@gmail.com',
    'iyanadarma171@gmail.com',
    'salmasetiulfi@gmail.com',
    'andiniwinda57@gmail.com',
    'qisashafrina@gmail.com',
    'xusupieza2020@gmail.com',
    'taufiq2002@gmail.com',
    'fifinur248@gmail.com',
    'rrndydandy@gmail.com',
    'rukmaniar994@gmail.com',
    'ochakumzati@gmail.com',
    'raihanarham300@gmail.com',
    'kurniasihnia2828@gmail.com'
);
```

###### Clean up users data 
```
 php artisan db:seed --class=CleanupEmployeeUsersSeeder
```

##### Update user admin 
```
php artisan db:seed --class=AdminCompanySeeder
``` 
