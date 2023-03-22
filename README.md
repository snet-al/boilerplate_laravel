# boilerplate_laravel

## Install and run

`1. composer install` -> Install required dependencies

`2. cp .env.example .env` -> Create the .env file and then set your configuration here

`3. php artisan key:generate` 

`4. php artisan migrate`

`5. php artisan passport:install`

`6. valet link` -> Run the project using valet (https://laravel.com/docs/10.x/valet)

## MultiApp Commands

`php artisan make:model ModelName --app=api` -> Add `app` option to generate the `Model` into a separate app.

`php artisan make:controller ControllerName --app=api` -> Add `app` option to generate the `Controller` into a separate app.

`php artisan make:request RequestName --app=api` -> Add `app` option to generate the `Request` into a separate app.

`php artisan make:resource ResourceName --app=api` -> Add `app` option to generate the `Resource` into a separate app.
