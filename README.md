Application Template
================================

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    models/              contains model classes used in both backend and frontend
    services/            contains main classes for managing headlines
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
    services/            contains main classes for parser
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    services/            contains main classes
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```

REQUIREMENTS
------------

The minimum requirement by this application template that your Web server supports PHP 5.4

CONFIGURATION
-------------

### Database

Edit the file `common/config/db.php` with real data, for example:

```php
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'mysql:host=localhost;dbname=...',
    'username' => '',
    'password' => '',
    'tablePrefix' => '',
    'charset'  => 'utf8',
];
```