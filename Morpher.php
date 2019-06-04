<?php

namespace arkhipovandrei\morpher;

use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\HttpException;


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

    /** @var yii\httpclient\Client $client*/
    public $client;
    public $token = '8728f695-e24b-4ade-be4f-55b058c03101';
    private $_query;
    private $_plural = false;
    private $_case = null;

    public function init()
    {
        if (empty($this->client)) {
            $this->client = (new Client([
                'baseUrl' => "{$this->baseUrl}/{$this->language}"
            ]));

        }
    }

    /**
     * @param $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->_query = $query;
        return $this;
    }

    public function setPlural()
    {
        $this->_plural = true;
        return $this;
    }

    /**
     * @param $case
     * @return $this
     */
    public function setCase($case)
    {
        $this->_case = $case;
        return $this;
    }

    /**
     * @return array|string|null
     * @throws Exception
     */
    public function getData()
    {
        $response = $this->fetchData();

        if (empty($response)) {
            return null;
        }

        if (key_exists('code', $response)) {

            $code = ArrayHelper::getValue($response, 'code');
            $message = ArrayHelper::getValue($response, 'message');

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

        if ($this->_case === null) {
            return $response;
        }

        if ($this->_plural) {
            return ArrayHelper::getValue($response, [self::PLURAL, $this->_case]);
        }

        return ArrayHelper::getValue($response, $this->_case);
    }

    public function getQuery()
    {
        return $this->_query;
    }

    public function getCase()
    {
        return $this->_case;
    }

    /**
     * Склонение
     *  'flags' = 'feminine,name'
     * @param null $flags
     * @return array|null
     * @throws HttpException
     */
    public function declension($flags = null)
    {
        $params = ['s' => $this->_query];

        if($flags) {
            $params['flags'] = $flags;
        }

        return $this->fetchData('declension', $params);
    }

    /**
     * Пропись чисел и согласование с числом
     * @param $n
     * @param $unit
     * @return array|null
     * @throws HttpException
     */
    public function spell($n, $unit)
    {
        return $this->fetchData('spell', [
            'n' => $n,
            'unit' => $unit
        ]);
    }

    /**
     * Склонение прилагательных по родам
     * @return array|null
     * @throws HttpException
     */
    public function genders()
    {
        return $this->fetchData('genders', ['s' => $this->_query]);
    }

    /**
     * Функция образует прилагательные от названий городов и стран
     *  Москва – московский, Ростов – ростовский, Швеция – шведский
     * @return array|null
     * @throws HttpException
     */
    public function adjectivize()
    {
        return $this->fetchData('adjectivize', ['s' => $this->_query]);
    }

    /**
     * @return array|null
     * @throws HttpException
     */
    private function fetchData($url, $params)
    {
        if($this->token) {
            $params['token'] = $this->token;
        }
        $response = $this->client
            ->get($url, $params)
            ->send();

        if ($response->isOk) {
            return $response->data;
        }

        throw new HttpException($response->statusCode, 'Morpher service error');
    }
}