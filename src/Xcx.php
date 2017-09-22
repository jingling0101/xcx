<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 2017/8/10
 * Time: 15:07
 */

namespace JLWx\Xcx;

class Xcx
{
    private static $appId;
    private static $secret;
    private static $code2session_url;
    private static $sessionKey;
    private static $cryptKey;
    private static $cryptIv;

    public function __construct()
    {
        self::$appId = config('xcx.appid', '');
        self::$secret = config('xcx.secret', '');
        self::$code2session_url = config('xcx.code2session_url', '');
        self::$cryptKey = config('xcx.cryptKey', '');
        self::$cryptIv = config('xcx.cryptIv', '');
    }

    /**
     * @return mixed
     */
    static public function getLoginInfo($code)
    {
        return self::authCodeAndCode2session($code);
    }

    static public function getUserInfo($encryptedData, $iv)
    {
        $aes = new WxBizDataCrypt(self::$appId, self::$sessionKey);
        $decode = '';
        $res = $aes->decryptData($encryptedData, $iv, $decode);
        if ($res != 0) {
            return [
                'code' => 1001,
                'msg' => '解密失败'
            ];
        }
        return $decode;
    }


    /**
     *
     * 根据 code 获取 session_key 等相关信息
     * @throws \Exception
     *
     */
    static private function authCodeAndCode2session($code)
    {
        $code2session_url = sprintf(self::$code2session_url, self::$appId, self::$secret, $code);
        //return self::$appId;
        $userInfo = Xcx::httpRequest($code2session_url);
//        if(!isset($userInfo['session_key'])){
//            return [
//                'code' => 10000,
//                'msg' => '获取 session_key 失败',
//            ];
//        }
        return self::setSerSession($userInfo);
    }

    static public function setSerSession($userInfo)
    {
        $session = $userInfo['openid'] . "_" . $userInfo['session_key'];
        $serSessionKey = self::myOpensslDecrypt($userInfo['openid'], self::$cryptKey, self::$cryptIv);
        session([$serSessionKey => $session]);
        return $serSessionKey;
    }

    static public function myOpensslEncrypt($data, $key, $iv, $method = 'aes-256-cbc')
    {
//        echo '<br>';
//        echo base64_encode(openssl_random_pseudo_bytes(32));
//        echo '<br>';
//        echo base64_encode(openssl_random_pseudo_bytes(16));
//        echo '<br>';
        $encrypted = openssl_encrypt($data, $method, base64_decode($key), OPENSSL_RAW_DATA, base64_decode($iv));
        return base64_encode($encrypted);
    }

    static public function myOpensslDecrypt($data, $key, $iv, $method = 'aes-256-cbc')
    {
        $encrypted = base64_decode($data);
        $encrypted = openssl_encrypt($encrypted, $method, base64_decode($key), OPENSSL_RAW_DATA, base64_decode($iv));
        return base64_encode($encrypted);
    }


    /**
     * @return mixed
     */
    static public function httpRequest($url, $data = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_PORT, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        if (!$res) {
            die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
        }
        curl_close($curl);
        return json_decode($res, JSON_UNESCAPED_UNICODE);
    }


}