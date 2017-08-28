# laravel-cms
Medias library for laravel

## Installation and setup

On a fresh Laravel (>= v5.4) installation, install the package by running:
```bash
composer require helori/laravel-cms
```

Configure your application:
```php
// config/app.php
'providers' => [
    ...
    Helori\LaravelMedias\MediasServiceProvider::class,
    Intervention\Image\ImageServiceProvider::class,
];

'aliases' => [
    ...
    'Image' => Intervention\Image\Facades\Image::class,
];
```

Run the migrations:
```bash
php artisan migrate
```

Publish the laravel-cms default assets and Vue components:
```bash
php artisan vendor:publish --tag=laravel-medias-assets
```

At the time of this writing, Laravel 5.4 ships with the following dependencies pre-configured in your package.json:
```json
"devDependencies": {
    "axios": "^0.15.3",
    "bootstrap-sass": "^3.3.7",
    "cross-env": "^3.2.3",
    "jquery": "^3.1.1",
    "laravel-mix": "0.*",
    "lodash": "^4.17.4",
    "vue": "^2.1.10"
}
```

Install these dependencies by running: 
```bash
npm install
```

Install additionnal dependencies required by this package:
```bash
npm install font-awesome --save-dev
```

Compile your assets :
```bash
npm run dev
```

