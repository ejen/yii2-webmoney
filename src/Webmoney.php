<?php

namespace ejen\payment;

use ejen\payment\webmoney\Payment;

class Webmoney extends \yii\base\Component
{
    public $baseUrl = 'https://merchant.webmoney.ru/lmi/payment.asp';

    public $secretKey;
    public $checksumMethod = 'SHA256';
    /**
     * @var array
     */
    public $purses;
    /**
     * для обратной совместимости
     * @var string
     */
    public $purse;
    public $resultUrl;
    public $successUrl;
    public $successMethod;
    public $failUrl;
    public $failMethod;

    public function createPayment($params)
    {
        $params['component'] = $this;
        return new Payment($params);
    }
}
