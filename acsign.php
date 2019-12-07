<?php

/**
 * Author: BANKA2017
 * Version: 4.2
 */
class Acsign{
    public $username, $password, $date, $access_token, $auth_key, $acPasstoken;
    private $ch;
    private function scurl($url) {
        $this -> ch = curl_init();
        curl_setopt($this -> ch, CURLOPT_URL, $url);
        curl_setopt($this -> ch, CURLOPT_USERAGENT, 'acvideo core/6.11.1.822');
        curl_setopt($this -> ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this -> ch, CURLOPT_RETURNTRANSFER, true);
        return $this;
    }

    /*获取日期*/
    public function get_date() {
        return date("Ymd");
    }

    /*客户端登录*/
    public function mo_login() {
        self::scurl('https://id.app.acfun.cn/rest/app/login/signin');
        curl_setopt($this -> ch, CURLOPT_POST, true);
        curl_setopt($this -> ch, CURLOPT_POSTFIELDS, http_build_query(["username" => $this -> username, "password" => $this -> password]));
        curl_setopt($this -> ch, CURLOPT_HTTPHEADER, ["deviceType: 1"]);
        $json = json_decode(curl_exec($this -> ch), true);
        if(!$json["result"]){
            $this->access_token = $json["token"];
            $this->acPasstoken = $json["acPassToken"];
            $this->auth_key = $json["auth_key"];
        }
        curl_close($this -> ch);
        return $json["result"];
    }

    /*客户端签到接口*/
    public function mo_nsign()
    {
        if ($this->get_date() > $this -> date) {
            self::scurl('https://api-new.acfunchina.com/rest/app/user/signIn');
            curl_setopt($this -> ch, CURLOPT_POST, true);
            curl_setopt($this -> ch, CURLOPT_POSTFIELDS, http_build_query(["access_token" => $this->access_token]));
            curl_setopt($this -> ch, CURLOPT_HTTPHEADER, ["acPlatform: ANDROID_PHONE", "Cookie: auth_key={$this->auth_key};acPasstoken={$this->acPasstoken}"]);
            $sign = json_decode(curl_exec($this -> ch), true);
            curl_close($this -> ch);
            $this->signed_date = $this->get_date();
            return $sign["msg"];
        } else {
            return "今日已签到";
        }
    }

    /*客户端检查签到接口*/
    public function c_sign() {
        self::scurl('https://api-new.acfunchina.com/rest/app/user/hasSignedIn');
        curl_setopt($this -> ch, CURLOPT_POST, true);
        curl_setopt($this -> ch, CURLOPT_POSTFIELDS, http_build_query(["access_token" => $this->access_token]));
        curl_setopt($this -> ch, CURLOPT_HTTPHEADER, ["acPlatform: ANDROID_PHONE", "Cookie: auth_key={$this->auth_key};acPasstoken={$this->acPasstoken}"]);
        $sign = json_decode(curl_exec($this -> ch), true);
        curl_close($this -> ch);
        return $sign["hasSignedIn"] ?? false;
    }

    /*显示*/
    public function display() {
        echo $this->username . " -> sign:" . self::mo_nsign() . "\n";
    }
}