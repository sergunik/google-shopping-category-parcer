[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sergunik/google-shopping-category-parcer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sergunik/google-shopping-category-parcer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sergunik/google-shopping-category-parcer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sergunik/google-shopping-category-parcer/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/sergunik/google-shopping-category-parcer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/sergunik/google-shopping-category-parcer/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/sergunik/google-shopping-category-parcer/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)


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
