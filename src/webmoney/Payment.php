<?php

namespace ejen\payment\webmoney;

use Yii;
use yii\helpers\Html;

class Payment extends \yii\base\Model
{
    public $component;

    public $id;
    public $amount;
    public $description;
    public $email;

    public $purse;
    public $secretKey;
    public $resultUrl;
    public $successUrl;
    public $successMethod;
    public $failUrl;
    public $failMethod;

    public $customFields = [];

    public function rules()
    {
        // @todo Check purse
        return [];
    }

    public function renderHiddenForm()
    {
        echo Html::beginForm($this->component->baseUrl, 'post', ['csrf' => false, 'name' => 'wm_hidden_form']);

        foreach($this->fields as $name => $value)
        {
            echo Html::hiddenInput($name, $value);
        }

        echo Html::endForm();

        echo Html::tag('script', "document.forms.wm_hidden_form.submit();");
    }

    public function getFields()
    {
        // Attributes for Payment class only
        $onlyAttributes = [
            'id' => 'LMI_PAYMENT_NO',
            'email' => 'LMI_PAYER_EMAL',
        ];

        // Attributes both for Payment class and Webmoney component
        $bothAttributes = [
            'resultUrl' => 'LMI_RESULT_URL',
            'successUrl' => 'LMI_SUCCESS_URL',
            'successMethod' => 'LMI_SUCCESS_METHOD',
            'failUrl' => 'LMI_FAIL_URL',
            'failMethod' => 'LMI_FAIL_METHOD',
        ];

        $fields = [
            'LMI_PAYEE_PURSE' => $this->purse ? $this->purse : $this->component->purse,
            'LMI_PAYMENT_AMOUNT' => $this->amount,
            'LMI_PAYMENT_DESC_BASE64' => base64_encode($this->description),
        ];

        foreach($onlyAttributes as $attribute => $lmi)
        {
            if ($this->{$attribute}) $fields[$lmi] = $this->{$attribute};
        }

        foreach($bothAttributes as $attribute => $lmi)
        {
            if ($this->{$attribute})
            {
                $fields[$lmi] = $this->{$attribute};
            }
            elseif ($this->component->{$attribute})
            {
                $fields[$lmi] = $this->component->{$attribute};
            }
        }

        foreach($this->customFields as $field)
        {
            $fields[$field['name']] = $field['value'];
        }

        return $fields;
    }
}
