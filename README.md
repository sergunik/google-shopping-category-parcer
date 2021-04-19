![Packagist Downloads](https://img.shields.io/packagist/dt/sergunik/google-shopping-category-parcer)
![Scrutinizer build](https://img.shields.io/scrutinizer/build/g/sergunik/google-shopping-category-parcer/master)
![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/sergunik/google-shopping-category-parcer/master)
![Scrutinizer coverage (GitHub/BitBucket)](https://img.shields.io/scrutinizer/coverage/g/sergunik/google-shopping-category-parcer/master)


# Google Shopping Category Parcer
Download Google Shopping Category, parse it and return as an array or json.

## Installation

```bash
composer require sergunik/google-shopping-category-parcer
``` 

## Usage

```php
use GSCP\GSCPService;

$service = new GSCPService();
$array = $service->toArray();
//or echo $service->toJson();
```

Or with parameters:
```php
use GSCP\GSCPService;

$service = new GSCPService();
$service->setLocale('uk_UA') //or other format uk-UA
    ->setFilename('storage/my-local-file.txt') //if you want to specify cache file
    ->setColumns([ //these columns by default 
        'id',
        'name',
        'parentId',
        'parents',
        'children',
    ])  
    ->toArray();
```

Also you could setup parameters in constructor:
```php
use GSCP\GSCPService;

$service = new GSCPService([
    'locale' => 'uk_UA',
    'filename' => 'storage/my-local-file.txt',
    'columns' => ['id', 'name']
]);
$service->toArray();
```
