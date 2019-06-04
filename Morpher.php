<?php

namespace arkhipovandrei\morpher;

use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\HttpException;

/**
 * Class Morpher
 * @package arkhipovandrei\morpher
 */
class Morpher extends Component
{
    const GENETIVE = 'Р';
    const DATIVE = 'Д';
    const ACCUSATIVE = 'В';
    const INSTRUMENTAL = 'Т';
    const PREPOSITIONAL = 'П';
    const PLURAL = 'множественное';

    const LANGUAGE_RU = 'russian';
    const LANGUAGE_UK = 'ukrainian';

    public $baseUrl = 'https://ws3.morpher.ru';
    public $language = self::LANGUAGE_RU;

    /** @var yii\httpclient\Client $client */
    public $client;
    public $token;
    public $data;

    public function init()
    {
        if (empty($this->client)) {
            $this->client = (new Client([
                'baseUrl' => "{$this->baseUrl}/{$this->language}"
            ]));
        }
    }

    /**
     * Получаем слово в заданном падеже
     * @param string $text
     * @param string $case
     * @param bool $plural
     * @param string|null $flags
     * @return string
     */
    public function declensionCase(string $text, string $case, bool $plural = false, string $flags = null): string
    {
        return (string)ArrayHelper::getValue($this
            ->declension($text, $flags)
            ->data, $plural ? [self::PLURAL, $case] : [$case]);
    }

    /**
     * Склонение
     *  'flags' = 'feminine,name'
     * @param null $flags
     * @return array|null
     * @throws HttpException
     */
    public function declension(string $text, string $flags = null)
    {
        $params = ['s' => $text];
        if ($flags) {
            $params['flags'] = $flags;
        }
        $this->data = $this->fetchData('declension', $params);
        return $this;
    }

    /**
     * Пропись чисел и согласование с числом
     * @param $n
     * @param $unit
     * @return array|null
     * @throws HttpException
     */
    public function spell(float $n, string $unit)
    {
        $this->data = $this->fetchData('spell', [
            'n' => $n,
            'unit' => $unit
        ]);
        return $this;
    }

    /**
     * Склонение прилагательных по родам
     * @return array|null
     * @throws HttpException
     */
    public function genders(string $text)
    {
        $this->data = $this->fetchData('genders', ['s' => $text]);
        return $this;
    }

    /**
     * Функция образует прилагательные от названий городов и стран
     *  Москва – московский, Ростов – ростовский, Швеция – шведский
     * @return array|null
     * @throws HttpException
     */
    public function adjectivize(string $text)
    {
        $this->data = $this->fetchData('adjectivize', ['s' => $text]);
        return $this;
    }

    /**
     * Запрос на сервер.
     * @param $url
     * @param $params
     * @return mixed
     */
    private function fetchData($url, $params)
    {
        if ($this->token) {
            $params['token'] = $this->token;
        }
        $response = $this->client
            ->get($url, $params)
            ->send();

        if ($response->isOk) {
            $data = $response->data;
        } else {
            throw new HttpException($response->statusCode, 'Morpher service error');
        }

        if (key_exists('code', $data)) {

            $code = ArrayHelper::getValue($data, 'code');
            $message = ArrayHelper::getValue($data, 'message');

            if (empty($message)) {

                switch ($code) {
                    case 1 :
                        $message = 'Превышен лимит на количество запросов в сутки. Перейдите на следующий тарифный план.';
                        break;
                    case 2 :
                        $message = 'Превышен лимит на количество одинаковых запросов в сутки. Реализуйте кеширование.';
                        break;
                    case 3 :
                        $message = 'IP заблокирован.';
                        break;
                    case 4 :
                        $message = 'Склонение числительных в GetXml не поддерживается. Используйте метод Propis.';
                        break;
                    case 5 :
                        $message = 'Не найдено русских слов.';
                        break;
                    case 6 :
                        $message = 'Не указан обязательный параметр s.';
                        break;
                    case 7 :
                        $message = 'Необходимо оплатить услугу.';
                        break;
                    case 8 :
                        $message = 'Пользователь с таким ID не зарегистрирован.';
                        break;
                    case 9 :
                        $message = 'Неправильное имя пользователя или пароль.';
                        break;
                    default :
                        $message = 'Неизвестный тип ошибки попробуйте позже.';
                }
            }
            throw new Exception("Morpher service error (code: {$code}): {$message}");
        }
        return $data;
    }

}