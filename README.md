# PHP-CRUD-ADMIN

A database admin interface for MySQL, PostgreSQL or SQL Server in a single file PHP script.

## Requirements

  - PHP 7.0 or higher with PDO drivers for MySQL, PgSQL or SqlSrv enabled

## Installation

This is a single file application! Upload "`admin.php`" somewhere and enjoy!

For local development you may run PHP's built-in web server:

    php -S localhost:8080

Test the script by opening the following URL:

    http://localhost:8080/admin.php/

Don't forget to modify the configuration at the bottom of the file.

## Configuration

Use the 'api' config parameter to configure the embedded [PHP-CRUD-API](https://github.com/mevdschee/php-crud-api).

These are the most important 'api' configuration options and their default value between brackets:

- "driver": mysql, pgsql or sqlsrv (mysql)
- "address": Hostname of the database server (localhost)
- "port": TCP port of the database server (defaults to driver default)
- "username": Username of the user connecting to the database (no default)
- "password": Password of the user connecting to the database (no default)
- "database": Database the connecting is made to (no default)

For more information check out the [PHP-CRUD-API](https://github.com/mevdschee/php-crud-api) documentation.

## Compilation

You can compile all files into a single "`admin.php`" file using:

    php build.php

You can access the non-compiled code at the URL:

    http://localhost:8080/src/

The non-compiled code resides in the "`src`" and "`vendor`" directories. The "`vendor`" directory contains the dependencies.

## Updating dependencies

You can update all dependencies of this project using the following command:

    php update.php

This script will install and run Composer to update the dependencies.

NB: The update script will also patch the dependencies in the vendor directory for PHP 7.0 compatibility.

## Local or remote API

This script is powered by [PHP-CRUD-API](https://github.com/mevdschee/php-crud-api) and embeds this project. Alternatively, it can run against a remote (live) installation.

If you want to run this against a remote installation, then replace the 'api' config parameter with one called 'url' that holds the base URL of your PHP-CRUD-API installation.