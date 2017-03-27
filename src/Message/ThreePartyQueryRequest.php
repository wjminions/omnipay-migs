<?php

namespace Omnipay\Migs\Message;

/**
 * Migs Purchase Request
 */
class ThreePartyQueryRequest extends AbstractRequest
{
    protected $action = 'queryDR';
    //protected $action = 'capture';

    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'transactionId');

        $data = $this->getBaseData();
        unset($data['vpc_ReturnURL']);
        unset($data['vpc_OrderInfo']);
        unset($data['vpc_Locale']);
        unset($data['vpc_Amount']);

        $data['vpc_User'] = $this->getParameter('user');
        $data['vpc_Password'] = $this->getParameter('password');
//        $data['vpc_SecureHash']  = $this->calculateHash($data);
//
//        if ($this->getSecureHashType() === 'SHA256') {
//            $data['vpc_SecureHashType']  = $this->getSecureHashType();
//        }

        return $data;
    }

    public function sendData($data)
    {
        $redirectUrl = $this->getEndpoint();

        $postData = "";

        $ampersand = "";
        foreach($data as $key => $value) {
            // create the POST data input leaving out any fields that have no value
            if (strlen($value) > 0) {
                $postData .= $ampersand . urlencode($key) . '=' . urlencode($value);
                $ampersand = "&";
            }
        }

        ob_start();
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $redirectUrl);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec ($ch);
        $response = ob_get_contents();
        ob_end_clean();

        $message = "";

        if(strchr($response,"<html>") || strchr($response,"<html>")) {;
            $message = $response;
        } else {
            if (curl_error($ch))
                $message = "%s: s". curl_errno($ch) . "<br/>" . curl_error($ch);
        }

        curl_close ($ch);

        $map = array();
        if (strlen($message) == 0) {
            $pairArray = explode("&", $response);
            foreach ($pairArray as $pair) {
                $param = explode("=", $pair);
                $map[urldecode($param[0])] = urldecode($param[1]);
            }
        }

        return $map;
    }

    public function getEndpoint()
    {
        return $this->endpoint.'vpcdps';
    }
}
