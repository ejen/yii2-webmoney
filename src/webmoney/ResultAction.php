<?php

namespace ejen\payment\webmoney;

use Yii;

class ResultAction extends \yii\base\Action
{
    public $componentId = 'webmoney';

    public $successCallback;

    public function run()
    {
        $hash = $this->calculateHash($_POST);
        if ($_POST['LMI_HASH2'] != $hash) return;
        
        if (!$this->successCallback)
        {
            Yii::$app->end();
        }
        return call_user_func_array($this->successCallback, [$_POST]);
    }

    protected function calculateHash($params)
    {
        // Берем секретный ключ из настроек компонента
        $params['LMI_SECRET_KEY'] = $this->component->secretKey;

        $fields = [
            'LMI_PAYEE_PURSE',
            'LMI_PAYMENT_AMOUNT',
            'LMI_HOLD',
            'LMI_PAYMENT_NO',
            'LMI_MODE',
            'LMI_SYS_INVS_NO',
            'LMI_SYS_TRANS_NO',
            'LMI_SYS_TRANS_DATE',
            'LMI_SECRET_KEY',
            'LMI_PAYER_PURSE',
            'LMI_PAYER_WM'
        ];

        $values = [];
        foreach($fields as $field)
        {
            if (isset($params[$field]))
            {
                $values[] = $params[$field];
            }
        }
        $string = implode(';', $values);

        switch(strtoupper($this->component->checksumMethod))
        {
            case 'SHA256':
                return strtoupper(hash('sha256', $string));
            case 'MD5':
                return strtoupper(md5($string));
            case 'SIGN':
                // @todo implement
                break;
            default:
                // @todo throw unsupported
                break;
        }
    }

    protected function getComponent()
    {
        return Yii::$app->{$this->componentId};
    }
}
