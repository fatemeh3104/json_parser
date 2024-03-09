
# Processmaker Utils 

This package includes functions that perform useful tasks related to managing forms and their data in ProcessMaker 4.

## Features
1. **Parsing config fields of form screens:**
    - Provides a process to parse config fields related to form screens in ProcessMaker 4.

2. **Storing items within screens:**
    - This package is capable of storing items related to screens in the database.

3. **Storing validations for each item in the database:**
    - Stores validations for each item in the database accurately.

4. **Validating items on the backend:**
    - The `utils` package can validate items on the backend when a request is sent from the frontend.

## Installation
To install this package, use the following commands:

* Use `composer require processmaker/utils` to install the package.
* Use `php artisan utils:install` to install generate the dependencies.


For initial installation and configuring screens, items, and validations, you can use the following command:

```
php artisan utils:install --first
```

## Uninstall
To uninstall the package, use the following commands:

```
php artisan utils:uninstall
composer remove processmaker/utils
```

These commands allow you to completely remove the package.

---

