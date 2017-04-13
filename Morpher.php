<?php 
namespace sevenfloor\morpher;

use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\HttpException;


class Morpher extends Component
{
    const API_URL = 'http://api.morpher.ru/WebService.asmx';
    const GENETIVE = 'Р';
    const DATIVE = 'Д';
    const ACCUSATIVE = 'В';
    const INSTRUMENTAL = 'Т';
    const PREPOSITIONAL = 'П';
    const PLURAL = 'множественное';

    private $_client;
    private $_query;
    private $_plural = false;
    private $_case = null;

    public function init()
    {
        $this->_client = (new Client(['baseUrl' => self::API_URL]));
    }

    /**
     * @return array|string|null
     * @throws Exception
     */
    public function getData(){
        $response = $this->fetchData();

        if(empty($response)) {
            return null;
        }

        if(key_exists('code', $response)) {

            $message = $response['message'];

            if(empty($message)) {
                switch ($response['code']) {
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

            throw new Exception("Morpher service error (code: {$response['code']}): ".$message);
        }

        if($this->_case === null) {
            return $response;
        }

        if($this->_plural && !empty($response[self::PLURAL])) {
            return ArrayHelper::getValue($response[self::PLURAL], $this->_case);
        }

        return ArrayHelper::getValue($response, $this->_case);
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

    public function getQuery()
    {
        return $this->_query;
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

    public function getCase()
    {
        return $this->_case;
    }

    public function setPlural()
    {
        $this->_plural = true;
        return $this;
    }

    /**
     * @return array|null
     * @throws HttpException
     */
    private function fetchData()
    {
        $response = $this->_client
            ->get('GetXml', ['s' => $this->_query])
            ->send();

        if($response->isOk) {
            return $response->data;
        }

        throw new HttpException($response->statusCode, 'Morpher service error');
    }
}