<?php

namespace Omnipay\Migs\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Migs Purchase Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        if (!is_array($data)) {
            parse_str($data, $data);
        }

        parent::__construct($request, $data);
    }

    public function isRedirect()
    {
        return false;
    }


    public function getRedirectUrl()
    {
        return false;
    }


    public function getRedirectHtml()
    {
        return false;
    }


    public function getTransactionNo()
    {
        return isset($this->data['vpc_ReceiptNo']) ? $this->data['vpc_ReceiptNo'] : null;
    }


    public function isSuccessful()
    {
        if (isset($this->data['vpc_TxnResponseCode']) && "0" === $this->data['vpc_TxnResponseCode']) {
            if (isset($this->data['vpc_AcqResponseCode'])) {
                if ("00" === $this->data['vpc_AcqResponseCode']) {
                    return true;
                }

                return false;
            }

            return true;
        }

        return false;
    }

    public function getTransactionReference()
    {
        return isset($this->data['vpc_ReceiptNo']) ? $this->data['vpc_ReceiptNo'] : null;
    }

    public function getMessage()
    {
        return isset($this->data['vpc_Message']) ? $this->data['vpc_Message'] : null;
    }
}
