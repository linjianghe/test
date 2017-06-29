<?php
/*
 * 时间：2016-11-14
 */

class DingTalk{

    private function getAccessToken()
    {
        /**
         * 保存accessToken。accessToken有效期为两小时，需要在失效前请求新的accessToken
         */

        $file = './uploads/dt_access_token.txt';

        if(!file_exists($file) or filectime($file) < time()-7000){

            $url = OAPI_HOST."/gettoken?corpid=".CORPID."&corpsecret=".SECRET;

            $response = json_decode($this->https_request($url));

            if($response->errcode){
                echo "<h3>error:</h3>" . $response->errcode;
                echo "<h3>msg  :</h3>" . $response->errmsg;
                echo "<h3>url  :</h3>" . $url;
                exit();
            }
            $access_token = $response->access_token;

            if ($access_token) {
                file_put_contents($file, $access_token);
            }
        } else{

            $access_token = trim(@file_get_contents($file));
        }
        return $access_token;
    }


    /*
     * 登录后台，使用SSO登录方式，使用的accessToken是不一样的
     * */
    private function getSSOAccessToken()
    {
        /**
         * 保存accessToken。accessToken有效期为两小时，需要在失效前请求新的accessToken
         */

        $file = './uploads/sso_access_token.txt';

        if(!file_exists($file) or filectime($file) < time()-7000){

            $url = OAPI_HOST."/sso/gettoken?corpid=".CORPID."&corpsecret=".SSOSECRET;

            $response = json_decode($this->https_request($url));

            if($response->errcode){
                echo "<h3>error:</h3>" . $response->errcode;
                echo "<h3>msg  :</h3>" . $response->errmsg;
                echo "<h3>url  :</h3>" . $url;
                exit();
            }
            $access_token = $response->access_token;

            if ($access_token) {
                file_put_contents($file, $access_token);
            }
        } else{

            $access_token = trim(@file_get_contents($file));
        }
        return $access_token;
    }


    private function getTicket()
    {
        /**
         * 保存Ticket。Ticket有效期为两小时
         */

        $file = './uploads/dt_Ticket.txt';

        if(!file_exists($file) or filectime($file) < time()-7000){

            $url = OAPI_HOST."/get_jsapi_ticket?access_token=".$this->getAccessToken();

            $response = json_decode($this->https_request($url));

            if($response->errcode){
                echo "<h3>error:</h3>" . $response->errcode;
                echo "<h3>msg  :</h3>" . $response->errmsg;
                echo "<h3>url  :</h3>" . $url;
                exit();
            }
            $ticket = $response->ticket;

            if ($ticket) {
                file_put_contents($file, $ticket);
            }
        } else{

            $ticket = trim(@file_get_contents($file));
        }
        return $ticket;
    }

    /*
     * 生成签名
     */
    public function getConfig()
    {
        $corpId = CORPID;
        $agentId = AGENTID;
        $nonceStr = $this->createNonceStr();
        $timeStamp = time();

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $corpAccessToken = $this->getAccessToken();
        if (!$corpAccessToken)
        {
            exit("[getConfig] ERR: no corp access token");
        }
        $ticket = $this->getTicket($corpAccessToken);
        $signature = $this->sign($ticket, $nonceStr, $timeStamp, $url);

        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'agentId' => $agentId,
            'timeStamp' => $timeStamp,
            'corpId' => $corpId,
            'signature' => $signature);
        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }


    private function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }

    private function createNonceStr($length = 16) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /*
     * 提交安全请求
     *
     */
    private function https_request($url,$data = null){

        $curl = curl_init();
        if(!empty($data)){
            $header = array("Content-type: application/json;charset='utf-8'");
            curl_setopt($curl, CURLOPT_HTTPHEADER  , $header);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /*
     *  获取userid
     */
    public function getInfo($code, $is_admin=0){

        if ( $is_admin ){
            $url = OAPI_HOST."/sso/getuserinfo?access_token=".$this->getSSOAccessToken()."&code=$code";
        }else{
            $url = OAPI_HOST."/user/getuserinfo?access_token=".$this->getAccessToken()."&code=$code";
        }

        $response = json_decode($this->https_request($url));
        if($response->errcode){
            echo "<h3>error:</h3>" . $response->errcode;
            echo "<h3>msg  :</h3>" . $response->errmsg;
            echo "<h3>Url  :</h3>" . $url;
            exit("授权");
        }
        if ( $is_admin ){
            $response = $response->user_info;
        }
        return $response;
    }

    /*
     *  根据userid 获取个人详情
     */

    public function getInfodetails($userid){

        $url = OAPI_HOST."/user/get?access_token=".$this->getAccessToken()."&userid=$userid";
        $response = json_decode($this->https_request($url));
        if($response->errcode){
            echo "<h3>error:</h3>" . $response->errcode;
            echo "<h3>msg  :</h3>" . $response->errmsg;
            echo "<h3>Url  :</h3>" . $url;
            exit("获取个人详情");
        }
        return $response;
    }

    /*
     *  根据部门id 获取部门详情
     */

    public function getdepartment($departid){

        $url = OAPI_HOST."/department/get?access_token=".$this->getAccessToken()."&id=$departid";
        $response = json_decode($this->https_request($url));
        if($response->errcode){
            echo "<h3>error:</h3>" . $response->errcode;
            echo "<h3>msg  :</h3>" . $response->errmsg;
            echo "<h3>Url  :</h3>" . $url;
            exit("获取部门详情");
        }
        return $response;
    }
    /*
     *  发送企业消息接口
     */

    public function send_message($data){

        $url = OAPI_HOST."/message/send?access_token=".$this->getAccessToken();
        $response = json_decode($this->https_request( $url , $data ));
        if($response->errcode){
            echo "<h3>error:</h3>" . $response->errcode;
            echo "<h3>msg  :</h3>" . $response->errmsg;
            echo "<h3>Url  :</h3>" . $url;
            exit("发送企业消息");
        }
        return $response;
    }
    /*
     *  获取企业会话消息已读未读状态接口
     */

    public function message_status($data){

        $url = OAPI_HOST."/message/list_message_status?access_token=".$this->getAccessToken();
        $response = json_decode($this->https_request( $url , $data));
        if($response->errcode){
            echo "<h3>error:</h3>" . $response->errcode;
            echo "<h3>msg  :</h3>" . $response->errmsg;
            echo "<h3>Url  :</h3>" . $url;
            exit("企业会话消息已读未读状态");
        }
        return $response;
    }


}

?>
