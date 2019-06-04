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

 ## Использование
 
```php
'components' => [
   // ...
    'morpher' => [
        'class' => 'arkhipovandrei\morpher\Morpher'
    ]
    // ...
 ]
 ```

Склонение
```php
   $morpher = Yii::$app
       ->morpher
       ->declension( 'Санкт-Петербург');
   
/*result 
    print_r($morpher->data);
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
 
Получить Слово в нужном патяже и числе 
```php
    $declensionCase = Yii::$app->morpher
        ->declensionCase( 'Название', Yii::$app->morpher::PREPOSITIONAL, true);
    //result Санкт-Петербургах
```
 
Пропись чисел и согласование с числом
```php
    $spell = Yii::$app
        ->morpher
        ->spell(100, 'рублей');
 ```
 
Склонение прилагательных по родам
```php
    $genders = Yii::$app
        ->morpher
        ->genders( 'рублей');
 ```
 
Функция образует прилагательные от названий городов и стран
*  Москва – московский, Ростов – ростовский, Швеция – шведский
```php
    $adjectivize = Yii::$app
        ->morpher
        ->adjectivize( 'рублей');
 ```




