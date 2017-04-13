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
        ->setQuery('Санкт-Петербург')
    ->getData();

     /*result 
     Array
     (
         [Р] => Санкт-Петербурга
         [Д] => Санкт-Петербургу
         [В] => Санкт-Петербург
         [Т] => Санкт-Петербургом
         [П] => Санкт-Петербурге
         [множественное] => Array
             (
                 [И] => Санкт-Петербурги
                 [Р] => Санкт-Петербургов
                 [Д] => Санкт-Петербургам
                 [В] => Санкт-Петербурги
                 [Т] => Санкт-Петербургами
                 [П] => Санкт-Петербургах
             )
     
     ); */
 ```
 
Get case; 
```php
    echo Yii::$app->morpher
        ->setQuery('Санкт-Петербург')
        ->setCase(Morpher::PREPOSITIONAL)
    ->getData();
    //result 'Санкт-Петербурге'
 ```

Get plural case; 
```php
    echo Yii::$app->morpher
        ->setQuery('Санкт-Петербург')
        ->setCase(Morpher::PREPOSITIONAL)
        ->setPlural()
    ->getData();
    
    //result Санкт-Петербургах
 ```


