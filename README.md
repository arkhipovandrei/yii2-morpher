# Yii2-morpher

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist arkhipovandrei/yii2-morpher
```

or add

```json
"arkhipovandrei/yii2-morpher": "*"
```

to the `require` section of your composer.json.

 ## Usage
 
 
```php
'components' => [
   // ...
    'morpher' => [
        'class' => 'arkhipovandrei\morpher\Morpher'
    ]
    // ...
 ]
 ```

Get all case; 
```php
    Yii::$app->morpher
        ->setQuery('Санкт-петербург')
    ->getData();
 ```
 
Get case; 
```php
    Yii::$app->morpher
        ->setQuery('Санкт-петербург')
        ->setCase(Morpher::PREPOSITIONAL)
    ->getData();
 ```

Get plural case; 
```php
    Yii::$app->morpher
        ->setQuery('Санкт-петербург')
        ->setCase(Morpher::PREPOSITIONAL)
        ->setPlural()
    ->getData();
 ```


