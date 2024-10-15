<?php

class UUAccount
{
    private $token;
    private $session;

    public function __construct($token)
    {
        $this->token = $token;
        $this->session = curl_init();
        curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->session, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json; charset=utf-8',
            'User-Agent: okhttp/3.14.9',
            'App-Version: 5.0.5',
            'Apptype: 4'
        ]);
    }

    private function random_str($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function send_login_sms_code($phone, $session)
    {
        $data = json_encode([
            'Mobile' => $phone,
            'Sessionid' => $session
        ]);

        $ch = curl_init('https://api.youpin898.com/api/user/Auth/SendSignInSmsCode');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public static function sms_sign_in($phone, $code, $session)
    {
        $data = json_encode([
            'Code' => $code,
            'Sessionid' => $session,
            'Mobile' => $phone
        ]);

        $ch = curl_init('https://api.youpin898.com/api/user/Auth/SmsSignIn');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function get_user_nickname()
    {
        return $this->call_api('GET', '/api/user/Account/getUserInfo')['Data']['NickName'];
    }

    private function call_api($method, $path, $data = null)
    {
        $url = 'https://api.youpin898.com' . $path;
        curl_setopt($this->session, CURLOPT_URL, $url);

        if ($method == 'GET') {
            if ($data) {
                $url .= '?' . http_build_query($data);
            }
            curl_setopt($this->session, CURLOPT_HTTPGET, true);
        } elseif ($method == 'POST') {
            curl_setopt($this->session, CURLOPT_POST, true);
            curl_setopt($this->session, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($this->session);
        return json_decode($response, true);
    }

    public function get_wait_deliver_list($game_id = 730, $return_offer_id = true)
    {
        $data = $this->call_api('POST', '/api/youpin/bff/trade/sale/v1/sell/list', [
            'keys' => '',
            'orderStatus' => '140',
            'pageIndex' => 1,
            'pageSize' => 100
        ]);

        $data_to_return = [];
        foreach ($data['data']['orderList'] as $order) {
            $data_to_return[] = [
                'order_id' => $order['tradeOfferId'],
                'item_name' => $order['productDetail']['commodityName']
            ];
        }
        return $data_to_return;
    }
}
