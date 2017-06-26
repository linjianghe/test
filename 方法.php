<?php
if (!function_exists('JSON返回')) {

	function JSON返回($状态码 , $状态说明 = "", $数据 = array()) {
		$返回数组 = array(
			'状态' => $状态码,
			'状态说明' => $状态说明,
			'数据' => $数据
		);
		echo json_encode($返回数组, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		exit(0);
	}

}

/*
*@code string 状态码 200成功/500失败
*@message string 状态说明
*@data array 返回数据
*/
if (!function_exists('api_json')) {

	function api_json($code , $message = "", $data = array()) {
		$arr = array(
			'code' => $code,
			'message' => $message,
			'data' => $data
		);
		echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		exit(0);
	}

}

if (!function_exists('账号格式验证')) {
    function 账号格式验证($账号){
        if(!preg_match("/^([a-zA-Z0-9_]+)$/",$账号)){
            JSON返回(500,'账号格式有误');
        }
        return true;
    }
}

if (!function_exists('年龄')) {
    function 年龄($身份证号 = '') {
        if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $身份证号)) {
            return false;
        }
        if(strlen($身份证号)==18){
            $年龄 = date('Y') - substr($身份证号,6,4);
        } else {
            $年龄 = date('Y') - ('19'. substr($身份证号,6,2));
        }
        return $年龄 ? $年龄 : false;
    }
}

if (!function_exists('手机格式验证')) {
    function 手机格式验证($手机号,$类型=''){
        if(!preg_match('/^13\d{9}|14[57]\d{8}|15[0-35-9]\d{8}|18\d{9}|170[0-35-9]\d{7}|171[89]\d{7}|17[678]\d{8}$/',$手机号)){
            $类型==''?JSON返回(500,'手机号格式有误'):api_json(10011,'phone number format error','手机号码格式有误');
        }
        return true;
    }
}

if (!function_exists('密码格式验证')) {
    function 密码格式验证($密码,$类型=''){
        if(!preg_match("/^[\w~!@#$%^&*()]{6,20}$/",$密码)){
            $类型==''?JSON返回(500,'密码格式有误'):api_json(10009,'password format error');
        }
        return true;
    }
}

if (!function_exists('身份证格式验证')) {
    function 身份证格式验证($身份证号,$类型=''){
        if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $身份证号)) {
            //$类型==''?JSON返回(500,'身份证格式错误'):api_json(10012,'ID card format error');
            if($类型==''){
                JSON返回(500,'身份证格式错误');
            } else if($类型=='硬件') {
                exit(507);//兼容体检数据接口
            } else {
                api_json(10012,'ID card format error','身份证格式有误');
            }
        }
        return true;
    }
}

if(!function_exists('生成邀请码')){
    function 生成邀请码($长度){
        $字符合集 = "1234567890abcdefghijklmnopqrstuvwxyz";
        $邀请码 = '';
        for ($i = 0; $i < $长度; $i++) {
            $邀请码 .= $字符合集{rand(0, strlen($字符合集) - 1)};
        }
        return $邀请码;
    }
}
if (!function_exists('加密密码')) {

    /**
     * 明文密码加密
     *
     * @return string 加密后的密码，长度32字节
     */
    function 加密密码($明文密码) {
        $长随机码 = md5(rand(1, pow(2, 31) - 1) . time());
        $随机码 = substr($长随机码, 0, 8);
        $加密的24位 = substr(md5($明文密码 . $随机码), 8);
        return $随机码 . $加密的24位;
    }

}

if (!function_exists('验证密码')) {

    /**
     * 密码验证
     *
     * @return boolean 验证通过返回true，失败返回false
     */
    function 验证密码($加密的密码, $明文密码) {
        $随机码 = substr($加密的密码, 0, 8);
        $旧加密的24位 = substr($加密的密码, 8);
        $加密的24位 = substr(md5($明文密码 . $随机码), 8);

        if ($旧加密的24位 == $加密的24位) {
            return true;
        } else {
            return false;
        }
    }

}

//短信发送
function POST数据($网址, $数据) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $网址);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $数据);
    $POST结果 = curl_exec($curl);
    curl_close($curl);
    return $POST结果;
}


function 验证码短信发送( $验证码, $手机号码  , $类型 = 0 , $参数=array()){


	$CI对象 = & get_instance();

	$短信接口		= $CI对象->config->item("短信接口");
	$短信接口配置	= $CI对象->config->item("alidayu短信接口配置");
	$佳燊短信接口配置	= $CI对象->config->item("佳燊短信接口配置");

	if( $短信接口 == "jiashen" ){

		$appkey     = $佳燊短信接口配置['appkey'];
		$cid	    = $佳燊短信接口配置['cid'];
		$secretkey	= $佳燊短信接口配置['secretkey'];

		if($类型==1) {
			$templateid	= 2;
			$param = '{"admin":"'.$参数['管理员'].'","product":"'.$参数['项目'].'","code":"'.$验证码.'"}';
		}
		elseif($类型==2) {
			$templateid	= 3;
			$param = '{"admin":"'.$参数['管理员'].'","product":"'.$参数['项目'].'","user":"'.$参数['帐号'].'","code":"'.$验证码.'"}';
		}
		elseif($类型==3) {
			$templateid	= 4;
			$param = '{"name":"'.$参数['姓名'].'","product":"'.$参数['项目'].'","admin":"'.$参数['管理员'].'","user":"'.$参数['帐号'].'"}';
		}else{
			$templateid	= 1;
			$param = '{"product":"E家小护士", "code":"'.$验证码.'", "expire":"20"}';
		}

		$time	= time();
		$md5	= MD5($cid . $appkey . $templateid . $secretkey . $time);

		$发送内容 = array(
			'cid'		=> $cid,
			'appkey'	=> $appkey,
			'templateid'=> $templateid,
			'param'		=> $param,
			'mobiles'	=> $手机号码,
			'time'		=> $time,
			'sign'		=> $md5,
		);

		$data = "";
		foreach ($发送内容 as $key => $val) {
			$data .= ("&" . $key . "=" . $val);
		}
		$data = substr($data, 1);

		//$发送地址 = 'http://local.admin.sms.com/Api/send/vcode';
		$发送地址 = 'https://smsdev.zoteri.net/Api/send/vcode';
		$发送验证码 = POST数据($发送地址, $data);

		//为了兼容银栗子，返回的成功code=0，改为code=0
		$数组 = json_decode($发送验证码,true);

		if( is_array($数组) and $数组["code"] == '0' ){
			$数组["code"] = "0000";
			$发送验证码 = json_encode($数组, JSON_UNESCAPED_UNICODE);
		}

		return $发送验证码;

	}

	if ( $短信接口 == "alidayu" ){
		
		require( APPPATH . "libraries/alidayu/TopSdk.php" );

		//date_default_timezone_set('Asia/Shanghai'); 
		//返回的JSON数据，例：{"result":{"err_code":"0","model":"102450312634^1103135028619","success":true},"request_id":"101yq89fptvl1"}

		$c = new TopClient;
		$c->appkey		= $短信接口配置['appkey'];
		$c->secretKey	= $短信接口配置['secretkey'];
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
		$req->setSmsFreeSignName( $短信接口配置['sign'] );
		$req->setSmsParam("{\"code\":\"".$验证码."\",\"expire\":\"20\"}");
		$req->setRecNum($手机号码);
		$req->setSmsTemplateCode( $短信接口配置['templatecode'] );
		$resp = $c->execute($req);

		$err_code = $resp->result->err_code;
		if( $err_code == '0' ){
			return '{"code":"0000"}';
		}else{
			return '{"code":"'.$err_code.'"}';
		}

	}


	$appkey = YLZ_APPKEY;
	$sid	= YLZ_SID;
	$token	= YLZ_TOKEN;
    //$tid	= 577;			//模板ID

    if($类型==1) {
        $tid	= 833;
        $param = '{"admin":"'.$参数['管理员'].'","product":"'.$参数['项目'].'","code":"'.$验证码.'"}';
    }
    elseif($类型==2) {
        $tid	= 834;
        $param = '{"admin":"'.$参数['管理员'].'","product":"'.$参数['项目'].'","user":"'.$参数['帐号'].'","code":"'.$验证码.'"}';
    }
    elseif($类型==3) {
        $tid	= 867;
        $param = '{"name":"'.$参数['姓名'].'","product":"'.$参数['项目'].'","admin":"'.$参数['管理员'].'","user":"'.$参数['帐号'].'"}';
    }
    else{
        $tid	= 577;
        $param = '{"product":"E家小护士", "code":"'.$验证码.'", "expire":"20"}';
    }

	$time	= time();
	$md5	= MD5($sid . $appkey . $tid . $token . $time);

    $发送内容 = array(
        'sid'		=> $sid,
        'appkey'	=> $appkey,
        'tid'		=> $tid,
        'param'		=> $param,
        'mobiles'	=> $手机号码,
        'time'		=> $time,
        'md5'		=> $md5,
    );

	$data = "";
	foreach ($发送内容 as $key => $val) {
		$data .= ("&" . $key . "=" . $val);
	}
	$data = substr($data, 1);

    $发送地址 = 'http://www.ylzsms.com/api/sendcode';
    $发送验证码 = POST数据($发送地址, $data);

    return $发送验证码;

}
/*
 * $内容={
    "name": "家佑大健康医疗团队",
    "time": "3月9号，16点52分",
    "result": "诊断：窦性心律左前分支阻滞。评论：窦性心律为正常的心律，左前分支阻滞建议行心脏彩超检查，如果无器质性心脏疾病，无需治疗，定期复查心电图即可。"
	}
 *
 */
function 消息短信发送($手机号码, $内容){
    $appkey = YLZ_APPKEY;
    $sid	= YLZ_SID;
    $token	= YLZ_TOKEN;
	$tid = 922;//心电判读结果
    $time	= time();
    $md5	= MD5($sid . $appkey . $tid . $token . $time);

    $发送内容 = array(
        'sid'		=> $sid,
        'appkey'	=> $appkey,
        'tid'		=> $tid,
        'param'		=> $内容,
        'mobiles'	=> $手机号码,
        'time'		=> $time,
        'md5'		=> $md5,
    );

    $data = "";
    foreach ($发送内容 as $key => $val) {
        $data .= ("&" . $key . "=" . $val);
    }
    $data = substr($data, 1);

    $发送地址 = 'http://www.ylzsms.com/api/sendcode';
    $发送验证码 = POST数据($发送地址, $data);

    return $发送验证码;

}


function 短信发送($短信内容,$手机号码){

	$appkey = YLZ_APPKEY;
	$sid	= YLZ_SID;
	$token	= YLZ_TOKEN;

	$time	= time();
	$md5	= MD5($sid . $appkey . $token . $time);

    $发送内容 = array(
        'sid'		=> $sid,
        'appkey'	=> $appkey,
        'content'	=> $短信内容,
        'mobiles'	=> $手机号码,
        'time'		=> $time,
        'md5'		=> $md5,
    );

	$data = "";
	foreach ($发送内容 as $key => $val) {
		$data .= ("&" . $key . "=" . $val);
	}
	$data = substr($data, 1);

    $发送地址 = 'http://www.ylzsms.com/api/send';
    $发送验证码 = POST数据($发送地址, $data);

    return $发送验证码;

	/*
    $发送内容 = array(
        '用户名' => '中钛新',
        '密码' => 'v8tXYrjBsLXrR7wB',
        '手机号' => $手机号码,
        '短信内容' => $短信内容,
        '签名' => '中钛新'
    );
    //$发送地址 = 'http://sms.jlzwifi.com/短信/发送短信';
    $发送地址 = 'http://sms.jlzwifi.com/api/send';
    $发送验证码 = POST数据($发送地址, json_encode($发送内容));
    return $发送验证码;
	*/
}

function 查询短信发送状态($短信编号,$手机号码){
    $发送内容 = array(
        '用户名' => '中钛新',
        '密码' => 'v8tXYrjBsLXrR7wB',
        '短信编号' => $短信编号,
        '手机号' => $手机号码
    );
    $发送地址 = 'http://sms.jlzwifi.com/短信/查询状态报告';
    $发送验证码 = POST数据($发送地址, json_encode($发送内容));
    return $发送验证码;
}


/*
* JPush推送，自定义消息推送消息给IOS、android设备（单条）
* https://www.jpush.cn/
* @参数 $平台 ios，android 必填
* @参数 $极光推送编号 必填
* @标题 $推送内容 可填
* @推送数据数组 type 必须有 1 体检分析 2 预警 3 添加成员 4 同意成员 (具体参数参考极光推送.docx)
*/
function JPush推送($平台, $极光推送编号, $弹框标题, $推送数据=array('type'=>0)){
    if(in_array($平台,array('ios','ipad','iphone'))){
        $平台 = 'ios';
    }
    if ($推送数据['type'] == 1 || $推送数据['type'] == 2 ) {
        $IOS声音 = 'warning.caf';
        $IOS图标背景 = '+1';
    } else if($推送数据['type'] == 5){
        $IOS声音 = 'default';
        $IOS图标背景 = '+1';
    } else {
        $IOS声音 = null;
        $IOS图标背景 = false;
    }

	require_once( APPPATH . "libraries/JPush/JPush.php");

	$app_key		= JPUSH_APP_KEY;
	$master_secret	= JPUSH_APP_SECRET;

	$client = new JPush($app_key, $master_secret);
    try {
        $result = $client->push()
            ->setPlatform($平台)
            ->addAlias($极光推送编号)
            ->setNotificationAlert($弹框标题)
            ->addAndroidNotification($弹框标题, '', 1, $推送数据)
            ->addIosNotification($弹框标题, $IOS声音, $IOS图标背景, true, 'iOS category', $推送数据)
            ->setOptions(100000, 3600, null, JPUSH_APP_CERT)
            ->send();
        //return  json_encode($result);
        return  $result->data->msg_id;
    } catch (Exception $e){
        return  $e->getmessage();
    }

}

/*
* JPush推送，自定义消息推送消息给IOS、android设备（群推）
* https://www.jpush.cn/
* @参数 $平台 ios，android 必填  all所有平台
* @标题 $推送内容 可填
* @推送数据数组 type 必须有 5 系统公告 (具体参数参考极光推送.docx)
*/

function JPush群推送($平台, $弹框标题, $推送数据=array('type'=>0)){
    //兼容IOS
    if ($推送数据['type'] == 1 || $推送数据['type'] == 2 ) {
        $IOS声音 = 'warning.caf';
        $IOS图标背景 = '+1';
    } else if($推送数据['type'] == 5){
        $IOS声音 = 'default';
        $IOS图标背景 = '+1';
    } else {
        $IOS声音 = null;
        $IOS图标背景 = false;
    }

    require_once(APPPATH . "libraries/JPush/JPush.php");

    $app_key = JPUSH_APP_KEY;
    $master_secret = JPUSH_APP_SECRET;

    $client = new JPush($app_key, $master_secret);
    try {
        $result = $client->push()
            ->setPlatform($平台)
            ->addAllAudience()
            ->setNotificationAlert($弹框标题)
            ->addAndroidNotification($弹框标题, '', 1, $推送数据)
            ->addIosNotification($弹框标题, $IOS声音, $IOS图标背景, true, 'iOS category', $推送数据)
            ->setOptions(100000, 3600, null, JPUSH_APP_CERT)
            ->send();
        //return  json_encode($result);
        return $result->data->msg_id;
    } catch (Exception $e) {
        return $e->getmessage();
    }
}


/*
* 生成心电图图片
* @参数: $data		, 心电图的数据
* @参数: $filepath  , 文件的保存路径
* @参数: $是否需要背景  , 0=不需要，1=需要
*/
function 生成心电图图片( $data, $filepath, $是否需要背景=0 ){

    if ( empty($data) ) return false;

    $目录 = dirname($filepath);
    if(!is_dir($目录)){
		mkdir($目录, 0777, true);
	}
    $len	= strlen( trim($data) );
    $arr	= array();

    for($i=0; $i<$len; $i+=32 ){

        $txt = substr( $data, $i, 32);

        $arr[1][] = hexdec( substr($txt, 0, 4) );
        $arr[2][] = hexdec( substr($txt, 4, 4) );
        $arr[6][] = hexdec( substr($txt, 8, 4) );
        $arr[7][] = hexdec( substr($txt, 12, 4) );
        $arr[8][] = hexdec( substr($txt, 16, 4) );
        $arr[9][] = hexdec( substr($txt, 20, 4) );
        $arr[10][] = hexdec( substr($txt, 24, 4) );
        $arr[11][] = hexdec( substr($txt, 28, 4) );

        $dd = count($arr[1]) - 1;

        $arr[0][] = $arr[1][$dd] - $arr[2][$dd] + 2048;					//I
        $arr[3][] = 4096 - ($arr[0][$dd] + $arr[1][$dd] ) / 2;			//aVR
        $arr[4][] = $arr[0][$dd] - ($arr[1][$dd] - 2048 ) / 2 ;			//aVL
        $arr[5][] = $arr[1][$dd] - ($arr[0][$dd] - 2048 ) / 2 ;			//aVF

        //if( count($arr[1]) > 3100 ) break;
    }

    /*全图缩放*/
    $zoom			= 0.48;
    $offset			= 170;

    $width			= 1200;				//图片整体宽度
    $high			= 110 * $zoom;

    $hh_count		= $high * 12 + 40;		//总高度
    $ecg_scope		= 0.15;				//心电图缩放比例

    //参数处理
    $allnum			= count($arr[1]);

    $y_pxdensity	= 1;									//y轴密度
    $x_pxdensity	= round($width/$allnum, 2);             //x轴密度


    //计算Y轴坐标
    $point_y = array();

    foreach( $arr as $ind => $tarr ){
        foreach($tarr as $val){
            $point_y[$ind][] = $ind * $high + $offset + -$val * $ecg_scope * $zoom;
        }
    }


    $empty_size_x = 26;		//左侧预留的空白，要写文字的


    //图片流开始
    //header("Content-type:image/png");
    $pic = imagecreate($width+$empty_size_x+10, $hh_count +20);

    //$pic = imagecreatefromjpeg('bg.jpg');

    imagecolorallocate($pic,255,245,255);			//背景色

    $color_1=imagecolorallocate($pic,157,96,3);		//线条色
    $color_2=imagecolorallocate($pic,0,0,0);		//黑色
    //$color_3=imagecolorallocate($pic,194,194,194);	//灰色
    $color_3=imagecolorallocate($pic,255,220,209);	//粉红色

    //绘制网格
    imagesetthickness($pic, 1);                 //网格线宽



    //写X轴的文字，秒数
    imagesetthickness($pic, 1);						//网格线宽
    $y_line_width	= floor($allnum/400);			//纵网格线数目
    $y_line_density	= $y_line_width==0 ? 0 :floor($width/$y_line_width); //纵网格线密度

    imagestring($pic, 2, $empty_size_x-1, $hh_count+4, "0", $color_2);	//零点数轴标记

    for($i=1;$i <= $y_line_width;$i++){            //绘制纵网格线
        imagesetthickness($pic, 1);                 //网格线宽
        //imageline($pic, $y_line_density*$i+$empty_size_x, 0, $y_line_density*$i+$empty_size_x, $hh_count,$color_3);
        imagestring($pic, 2, $y_line_density*$i+$empty_size_x-5,$hh_count+4, $i.'s',$color_2);    //数轴标记
    }

    $g_ww = $y_line_density/25;			//格子密度

    for($i=1;$i <= ($width/$g_ww); $i++){				//绘制纵网格线
        if ( $i%5==0 ){
            imagesetthickness($pic, 2);                 //网格线宽
        }else{
            imagesetthickness($pic, 1);                 //网格线宽
        }
        imageline($pic, $i*$g_ww+$empty_size_x, 0, $i*$g_ww+$empty_size_x, $hh_count, $color_3);
    }

    imagesetthickness($pic, 1);                 //网格线宽

    for($i=1; $i < ($hh_count/$g_ww); $i++){            //绘制横网格线
        imageline($pic, $empty_size_x, $i*$g_ww, $width+$empty_size_x, $i*$g_ww, $color_3);
    }


    //绘制轴线  X Y 两条主轴
    imagesetthickness($pic, 2);                    //轴线宽
    imageline($pic,1+$empty_size_x,0,1+$empty_size_x, $high * 12+30,$color_2);					//Y
    //imageline($pic,0+$empty_size_x, $high * 12+30, $width+$empty_size_x,$high * 12+30,$color_2);	//X


    //写横坐标文字
    $xname_arr = array( '  I', ' II', 'III', 'aVR', 'aVL', 'aVF', ' V1', ' V2', ' V3', ' V4', ' V5', ' V6');

    for($i=0; $i < 12; $i++){
        imagesetthickness($pic, 1);                //网格线宽
        imagestring($pic, 2, 5, $high * $i + 18, $xname_arr[$i], $color_2);    //数轴标记
    }

    //print_r($point_y[0]);exit;

    foreach( $point_y as $i => $tarr ){

        //产生折线
        $point_x=0;
        imagesetthickness($pic, 2);             //线条粗细

        foreach( $tarr as $j => $y ){
            if($j>=$allnum-1) continue;
            imageline($pic, $point_x+2+$empty_size_x, $tarr[$j], $point_x+$x_pxdensity+2+$empty_size_x, $tarr[$j+1], $color_1);
            $point_x+=$x_pxdensity;
        }

        //break;
    }

    //imagepng($pic);

    imagepng($pic, $filepath );		//保存图片

    imagedestroy($pic);

    return true;
}


function 导出Excel($title=array(),$data=array(),$name='Sheet1'){

    include_once(APPPATH . '/libraries/PHPExcel.php');

    $列 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheetindex(0);
    $objsheet = $objPHPExcel->getActiveSheet();
    $objsheet->setTitle($name);

    for($i=0;$i<count($title);$i++){
        if($i>=26){
            $首 = floor($i/26)-1;
            $余 = $i % 26;
            $objsheet->setCellValue($列[$首].$列[$余].'1',$title[$i]);
            $objsheet->getColumnDimension($列[$首].$列[$余])->setWidth(15);

            $objsheet->getStyle($列[$首].$列[$余].'1')->applyFromArray(
	            array(
	                'font' => array ('bold' => true),
	                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	            )
	        );

        }else{
            $objsheet->setCellValue($列[$i].'1',$title[$i]);
            $objsheet->getColumnDimension($列[$i])->setWidth(15);

            $objsheet->getStyle($列[$i].'1')->applyFromArray(
                array(
                    'font' => array ('bold' => true),
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                )
            );
        }
    }

    foreach($data as $key=>$val){

        for($i=0;$i<count($title);$i++){
            $排 = $key + 2;
            if($i>=26){
                $首 = floor($i/26)-1;
                $余 = $i % 26;
                $objsheet->setCellValue($列[$首].$列[$余].$排,$val[$title[$i]]);

                $objsheet->getStyle($列[$首].$列[$余].$排)->applyFromArray(
                    array(
                        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    )
                );
            }else{
                $objsheet->setCellValue($列[$i].$排,$val[$title[$i]]);
                $objsheet->getStyle($列[$i].$排)->applyFromArray(
                    array(
                        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    )
                );
            }
        }
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $filename = iconv('utf-8', 'gb2312', $name.'_'.time());

    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Content-Type:application/force-download');
    header('Content-Type:application/vnd.ms-execl');
    header('Content-Type:application/octet-stream');
    header('Content-Type:application/download');
    header("Content-Disposition:attachment;filename={$filename}.xlsx");
    header('Content-Transfer-Encoding:binary');
    $objWriter->save('php://output');
}
//根据周数获取日期，以周一为第一天
function getWeekDate($年,$周数){
    $firstday=mktime(0,0,0,1,1,$年);
    $weekday=date('N',$firstday);
    $firstweenum=date('W',$firstday);
    if($firstweenum==1){
        $day=(1-($weekday-1))+7*($周数-1);
        $startdate=date('Y-m-d',mktime(0,0,0,1,$day,$年));
        $enddate=date('Y-m-d',mktime(0,0,0,1,$day+6,$年));
    }else{
        $day=(9-$weekday)+7*($周数-1);
        $startdate=date('Y-m-d',mktime(0,0,0,1,$day,$年));
        $enddate=date('Y-m-d',mktime(0,0,0,1,$day+6,$年));
    }
    return array('开始时间'=>$startdate,'结束时间'=>$enddate);
}


/*根据时间戳转换位多久之前*/
function 多久之前($the_time) {
    if(!$the_time){
        return false;
    }
    $now_time = time();
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur <= 0) {
        return '一秒前';
    } else if($dur > 0 AND $dur < 60){
        return $dur . '秒前';
    } else if($dur > 60 AND $dur < 3600){
        return floor($dur/60) . '分前';
    } else if($dur > 3600 AND $dur < (3600*24)){
        return floor($dur/(3600)) . '小时前';
    } else if($dur > (3600*24) AND $dur < (3600*24*2)){
        return '昨天';
    } else {
        return date('Y-m-d',strtotime($the_time));
    }
    /*else if($dur > (3600*24*30) AND $dur < (3600*24*30*12)){
        return floor($dur/(3600*24*30)) . '月前';
    } else if($dur > (3600*24*30*12)) {
        return floor($dur/(3600*24*30*12)).'年前';
    }*/
}

function getCurl($url){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close ($ch);
    return $result;
}


/*
创建多级子目录
*/
function mkdirs($dir){
    return is_dir($dir) or (mkdirs(dirname($dir)) and mkdir($dir,0777));
}

/*
* 发送监控通知 -- 如果异常，就发送通知
* @参数: $标题，string
* @参数: $内容，string
* @参数: $接收人，string (支持多个以数组形式array())
*/
function 邮件发送($标题, $内容, $接收邮箱){
    include_once('./system/libraries/Email.php');
    $config['protocol'] = 'smtp';
    $config['smtp_host'] = SMTP_HOST;
    $config['smtp_user'] = SMTP_USER;
    $config['smtp_pass'] = SMTP_PASS;
    $config['smtp_port'] = 25;
    $config['smtp_timeout'] = 5;
    $config['mailtype'] = 'html';
    $config['charset'] = 'utf-8';
    $config['wordwrap'] = TRUE;
    $邮件类 = new CI_Email();
    $邮件类->initialize($config);
    $邮件类->from(SMTP_USER, '中钛新');
    $邮件类->to($接收邮箱);

    $邮件类->subject($标题);
    $邮件类->message($内容);
    $返回状态 = $邮件类->send();
    if($返回状态){
        return '邮件已发送';
    }else{
        return $邮件类->print_debugger(array('headers'));
    }
}

/*
 * js页面跳转, 因为使用系统提供的 redirect 方法，在钉钉应用内跳转时出错
 */
function js页面跳转( $链接 ){
    echo '<script>location.href="'.$链接.'"</script>';
    exit;
}

/*
 * 短信发送结果处理保存
 */
function 短信发送保存($手机号 , $临时密码 , $类型=0 ,$参数=array(),$保存类型 = '验证码',$限制次数 = 3 )
{
    $CI = & get_instance();
    $date = date('Y-m-d');
    $语句 = "select count(*) as num from 短信发送表 where 手机号 = {$手机号} and DATE(发送时间) = '{$date}' and 类型 = '{$保存类型}'";
    $手机验证次数 = $CI->db->query($语句)->row_array();
    if($限制次数){
        if ($手机验证次数['num'] >= $限制次数) {
            JSON返回(500, '短信发送次数已用完，请明天再试');
        }
    }
    $返回数据 = 验证码短信发送($临时密码,$手机号,$类型,$参数);
    $数组 = json_decode($返回数据, true);
    $状态 = $数组['code'] == '0000' ? '成功' : '失败';
    $验证数组 = array(
        '手机号'   => $手机号,
        '验证码'   => $临时密码,
        '状态'     => $状态,
        '返回码'   => $数组['code'],
        '类型'     => $保存类型
    );
    if(@$参数['内容']){
        $验证数组['自定义短信内容'] = $参数['内容'];
    }
    $状态 = $CI->db->insert('短信发送表',$验证数组);
    if($数组['code'] == '0000'){
        return true;
    }else{
        JSON返回(500,'短信发送失败');
    }
}

/*
 * 验证码验证
 */
function 验证码验证( $手机号 ,$验证码 ,$类型='验证码' ,$时效=1200)
{
    $CI = & get_instance();

    手机格式验证($手机号);

    $语句 = "select * from 短信发送表 where 手机号 = {$手机号} and 类型 = '{$类型}' order by 短信编号 desc limit 1";

    $手机验证码 = $CI->db->query($语句)->row_array();

    $时间 = time()-strtotime($手机验证码['发送时间']);

    if($时间>$时效){

        JSON返回(500,'验证码已过期');
    }

    if($验证码 != $手机验证码['验证码']){

        JSON返回(500,'请输入正确的验证码');
    }
    return true;
}

/*
 * 浏览器显示图片
 */
function 心电图解析(){
    $data=array();
    $otherData=array();
    $xml=new DOMDocument();
    $xml->load('assets/images/EDAN_FDAXml.xml'); //将XML中的数据,读取到数组对象中
    $subjectOf=$xml->getElementsByTagName('subjectOf');
    $annotationArry=$subjectOf->item(0)->getElementsByTagName('annotation');
    foreach($annotationArry as $annotation){
        $childValue=$annotation->getElementsByTagName('value');
        if($childValue->length!=0){
            $type=$childValue->item(0)->attributes->item(0)->nodeValue;
            if($type=='PQ'&&($annotation->getElementsByTagName('code')->length!=0)){
                $index=$annotation->getElementsByTagName('code')->item(0)->attributes->item(0)->nodeValue;
                $value=$childValue->item(0)->attributes->item(1)->nodeValue;
                $otherData[$index]=$value;
            }
        }
    }
    $component=$xml->getElementsByTagName('component');
    $sequenceArry=$component->item(1)->getElementsByTagName('sequence');
    foreach($sequenceArry as $sequence){
        $digits=$sequence->getElementsByTagName('digits');
        if($digits->length!=0){
            $code=$sequence->getElementsByTagName('code');
            $lineName=$code->item(0)->attributes->item(0)->nodeValue;
            $data[$lineName]=explode(" ",$digits->item(0)->nodeValue);
        }
    }
    Header("Content-type: image/png");
    header("Access-Control-Allow-Origin: *");
    $im = imagecreate(1910,1730);
    $A=imagecolorallocatealpha($im,255,255,255,127);
    $red = ImageColorAllocate($im, 255,0,0);
    $pink = imagecolorallocatealpha($im,255,0,0,100);
    $brown = ImageColorAllocate($im, 255,255,255);
    $white = ImageColorAllocate($im, 255,255,255);
    $black = ImageColorAllocate($im, 0,0,0);
    $blue = ImageColorAllocate($im, 0,64,255);
    $offsetY=72;
    $offsetX=50;
    $scal=0.18;
    $lineHeight=800;
    $bg=0;//0无背景，1有背景
    if($bg==1){
        imagefilledrectangle ( $im , 0 , 0 , 50 , 5000 , $black );
        $keyHeight=0;
        foreach ($data as $key => $value){
            imagestring($im,5,10,$keyHeight*$lineHeight*$scal+$offsetY-10,substr($key,13),$brown);
            $keyHeight++;
        }
        for($i=0;$i<9999;$i+=40){
            if($i%40==0){
                $lineColor=($i%200==0)?$red:$pink;
                imageline($im, 50,$i*$scal, 5000,$i*$scal, $lineColor);
            }
        }
    }
    for($i=0;$i<9999;$i++){
        if($i%40==0&&$bg==1){
            $lineColor=($i%200==0)?(($i%1000==0)?$blue:$red):$pink;
            imageline($im, $i*$scal+$offsetX,0, $i*$scal+$offsetX,5000, $lineColor);
            if($i%1000==0){
                imagestring($im,5,$i*$scal+$offsetX-8,10,($i/1000)."s",$blue);
                imagestring($im,5,$i*$scal+$offsetX-8,12*$lineHeight*$scal-30,($i/1000)."s",$blue);
            }
        }
        $lineIndex=0;
        foreach ($data as $line) {
            imagelinethick($im, $i*$scal+$offsetX, (-(int)(($line[$i])*$scal*2.52*2)+($lineHeight*$lineIndex))*$scal+$offsetY, ($i+1)*$scal+$offsetX, (-(int)(($line[$i+1])*$scal*2.52*2)+($lineHeight*$lineIndex))*$scal+$offsetY, $black,2);
            $lineIndex++;
        }
    }
    ImagePng($im);
    ImageDestroy($im);
    return true;
}

/*
 * $文件 心电图的xml源文件 例如：assets/images/EDAN_FDAXml.xml
 * $存放路径 例如：UPLOADS.'ecg'
 * $生成图片名称格式（png类型）  例如：a.png
 */
function 生成心电图($文件,$存放目录,$生成图片名称){
    if(!is_file($文件)){
        //JSON返回(500,'文件不存在');
        return false;
    }
    if(!is_dir($存放目录)){
        mkdirs($存放目录);
    }
    if(!$生成图片名称){
        //JSON返回(500,'图片名称不能为空');
        return false;
    }
    $data=array();
    $xml=new DOMDocument();
    $xml->load($文件); //将XML中的数据,读取到数组对象中
    $component=$xml->getElementsByTagName('component');
    $sequenceArry=$component->item(1)->getElementsByTagName('sequence');
    foreach($sequenceArry as $sequence){
        $digits=$sequence->getElementsByTagName('digits');
        if($digits->length!=0){
            $code=$sequence->getElementsByTagName('code');
            $lineName=$code->item(0)->attributes->item(0)->nodeValue;
            $data[$lineName]=explode(" ",$digits->item(0)->nodeValue);
        }
    }
    $im = imagecreate(1910,1730);
    $A=imagecolorallocatealpha($im,255,255,255,127);
    $red = ImageColorAllocate($im, 255,0,0);
    $pink = imagecolorallocatealpha($im,255,0,0,100);
    $brown = ImageColorAllocate($im, 255,255,255);
    $white = ImageColorAllocate($im, 255,255,255);
    $black = ImageColorAllocate($im, 0,0,0);
    $offsetY=72;
    $offsetX=50;
    $scal=0.18;
    $lineHeight=800;
    $bg=0;//0无背景，1有背景

    if($bg==1){
        for($i=0;$i<9999;$i+=40){
            if($i%40==0){
                $lineColor=($i%200==0)?$red:$pink;
                imageline($im, 0,$i*$scal+$offsetY, 5000,$i*$scal+$offsetY, $lineColor);
            }
        }
    }
    for($i=0;$i<9999;$i++){
        if($i%40==0&&$bg==1){
            $lineColor=($i%200==0)?$red:$pink;
            imageline($im, $i*$scal+$offsetX,0, $i*$scal+$offsetX,5000, $lineColor);
        }
        $lineIndex=0;
        foreach ($data as $line) {
            imagelinethick($im, $i*$scal+$offsetX, (-(int)(($line[$i])*$scal*2.52*2)+($lineHeight*$lineIndex))*$scal+$offsetY, ($i+1)*$scal+$offsetX, (-(int)(($line[$i+1])*$scal*2.52*2)+($lineHeight*$lineIndex))*$scal+$offsetY, $black,2);
            $lineIndex++;
        }
    }
    ImagePng($im,$存放目录.'/'.$生成图片名称);
    ImageDestroy($im);
    return true;
}

/*
 * $文件 心电图的xml源文件 例如：assets/images/EDAN_FDAXml.xml
 * $存放路径 例如：UPLOADS.'ecg'
 * $生成图片名称格式（png类型）  例如：a.png
 * 备注：生成带背景缩略图,APP专用
 */
function 生成带背景心电图($文件,$存放目录,$生成图片名称){
    if(!is_file($文件)){
        //JSON返回(500,'文件不存在');
        return false;
    }
    if(!is_dir($存放目录)){
        mkdirs($存放目录);
    }
    if(!$生成图片名称){
        //JSON返回(500,'图片名称不能为空');
        return false;
    }
    $data=array();
    $xml=new DOMDocument();
    $contents = file_get_contents($文件);
    $res = $xml->loadXML( $contents );
    if( !$res ){
        return false;
    }
    $component=$xml->getElementsByTagName('component');
    $sequenceArry=$component->item(1)->getElementsByTagName('sequence');
    foreach($sequenceArry as $sequence){
        $digits=$sequence->getElementsByTagName('digits');
        if($digits->length!=0){
            $code=$sequence->getElementsByTagName('code');
            $lineName=$code->item(0)->attributes->item(0)->nodeValue;
            $data[$lineName]=explode(" ",$digits->item(0)->nodeValue);
        }
    }
    $im = imagecreate(1910,1730);
    $A=imagecolorallocatealpha($im,255,255,255,127);
    $red = ImageColorAllocate($im, 255,0,0);
    $pink = imagecolorallocatealpha($im,255,0,0,100);
    $brown = ImageColorAllocate($im, 255,255,255);
    $white = ImageColorAllocate($im, 255,255,255);
    $black = ImageColorAllocate($im, 0,0,0);
    $blue = ImageColorAllocate($im, 0,64,255);
    $offsetY=72;
    $offsetX=50;
    $scal=0.18;
    $lineHeight=800;
    $bg=1;//0无背景，1有背景
    if($bg==1){
        imagefilledrectangle ( $im , 0 , 0 , 50 , 5000 , $black );
        $keyHeight=0;
        foreach ($data as $key => $value){
            imagestring($im,5,10,$keyHeight*$lineHeight*$scal+$offsetY-10,substr($key,13),$brown);
            $keyHeight++;
        }
        for($i=0;$i<9999;$i+=40){
            if($i%40==0){
                $lineColor=($i%200==0)?$red:$pink;
                imageline($im, 50,$i*$scal, 5000,$i*$scal, $lineColor);
            }
        }
    }
    for($i=0;$i<9999;$i++){
        if($i%40==0&&$bg==1){
            $lineColor=($i%200==0)?(($i%1000==0)?$blue:$red):$pink;
            imageline($im, $i*$scal+$offsetX,0, $i*$scal+$offsetX,5000, $lineColor);
            if($i%1000==0){
                imagestring($im,5,$i*$scal+$offsetX-8,10,($i/1000)."s",$blue);
                imagestring($im,5,$i*$scal+$offsetX-8,12*$lineHeight*$scal-30,($i/1000)."s",$blue);
            }
        }
        $lineIndex=0;
        foreach ($data as $line) {
            imagelinethick($im, $i*$scal+$offsetX, (-(int)(($line[$i])*$scal*2.52*2)+($lineHeight*$lineIndex))*$scal+$offsetY, ($i+1)*$scal+$offsetX, (-(int)(($line[$i+1])*$scal*2.52*2)+($lineHeight*$lineIndex))*$scal+$offsetY, $black,2);
            $lineIndex++;
        }
    }
    ImagePng($im,$存放目录.'/'.$生成图片名称);
    ImageDestroy($im);
    return true;
}

/*
 * $文件 心电图的xml源文件 例如：assets/images/EDAN_FDAXml.xml
 */
function 心电采样($文件){
    if(!is_file($文件)){
        //JSON返回(500,'文件不存在');
        return false;
    }
    $otherData=array();
    $xml=new DOMDocument();
    $contents = file_get_contents($文件);
    $res = $xml->loadXML( $contents );
    if( !$res ){
        return false;
    }
    $subjectOf=$xml->getElementsByTagName('subjectOf');
    $annotationArry=$subjectOf->item(0)->getElementsByTagName('annotation');
    foreach($annotationArry as $annotation){
        $childValue=$annotation->getElementsByTagName('value');
        if($childValue->length!=0){
            $type=$childValue->item(0)->attributes->item(0)->nodeValue;
            if($type=='PQ'&&($annotation->getElementsByTagName('code')->length!=0)){
                $index=$annotation->getElementsByTagName('code')->item(0)->attributes->item(0)->nodeValue;
                $value=$childValue->item(0)->attributes->item(1)->nodeValue;
                $otherData[$index]=$value;
            }
            if($type=='PQ'&&($annotation->getElementsByTagName('methodCode')->length!=0)){
                $index=$annotation->getElementsByTagName('methodCode')->item(0)->attributes->item(0)->nodeValue;
                $value=$childValue->item(0)->attributes->item(1)->nodeValue;
                $otherData[$index]=$value;
            }
            if($type=='ST'&&($annotation->getElementsByTagName('code')->length!=0)){
                $index=$annotation->getElementsByTagName('code')->item(0)->attributes->item(0)->nodeValue;
                $value=$childValue->item(0)->nodeValue;
                $otherData[$index]=$value;
            }
        }
    }
    return $otherData;
}



/*
 * $文件 心电图文件 例如：uploads/ecg/一导联心电图.txt
 * $存放路径 例如：UPLOADS.'ecg'
 * $生成图片名称格式（png类型）  例如：a.png
 * 备注：生成一导联心电图
 */
function 生成一导联心电图($文件,$存放目录,$生成图片名称,$背景=0){
    if(!is_file($文件)){
        //JSON返回(500,'文件不存在');
        return false;
    }
    if(!is_dir($存放目录)){
        mkdirs($存放目录);
    }
    if(!$生成图片名称){
        //JSON返回(500,'图片名称不能为空');
        return false;
    }
    $dataStr=file_get_contents($文件);
    $data=explode(",",$dataStr);
    $im = imagecreate(1910,1730);
    $A=imagecolorallocatealpha($im,255,255,255,127);
    $red = ImageColorAllocate($im, 255,0,0);
    $pink = imagecolorallocatealpha($im,255,0,0,100);
    $brown = ImageColorAllocate($im, 255,255,255);
    $white = ImageColorAllocate($im, 255,255,255);
    $black = ImageColorAllocate($im, 0,0,0);
    $blue = ImageColorAllocate($im, 0,64,255);
    $offsetY=395;
    $offsetX=50;
    $scal=0.18;
    $lineHeight=2000;
    $bg=$背景;//0无背景，1有背景
	//$bg=1;
    if($bg==1){
        imagefilledrectangle ( $im , 0 , 0 , 50 , 5000 , $black );
        $keyHeight=0;
        foreach ($data as $key => $value){
            imagestring($im,5,10,$keyHeight*$lineHeight*$scal+$offsetY-10,substr($key,13),$brown);
            $keyHeight++;
        }
        for($i=0;$i<9999;$i+=40){
            if($i%40==0){
                $lineColor=($i%200==0)?$red:$pink;
                imageline($im, 50,$i*$scal, 5000,$i*$scal, $lineColor);
            }
        }
		imagestring($im,5,10,0+$offsetY,"10s",$white);
	imagestring($im,5,10,360+$offsetY,"20s",$white);
	imagestring($im,5,10,720+$offsetY,"30s",$white);	
    }
    $lineIndex=0;
    for($y=0;$y<3;$y++){
        for($i=0;$i<1499;$i++){
            if($i%6==0&&$bg==1){
                $lineColor=($i%30==0)?(($i%150==0)?$blue:$red):$pink;
                imageline($im, $i*$scal*(1000/150)+$offsetX,0, $i*$scal*(1000/150)+$offsetX,5000, $lineColor);
                if($i%150==0){
					$wordOffset=100;
                    imagestring($im,5,$i*$scal*(1000/150)+$offsetX-8,$wordOffset+0+$offsetY,($i/150)."s",$blue);
                    imagestring($im,5,$i*$scal*(1000/150)+$offsetX-8,$wordOffset+360+$offsetY,(($i/150)+10)."s",$blue);
                    imagestring($im,5,$i*$scal*(1000/150)+$offsetX-8,$wordOffset+720+$offsetY,(($i/150)+20)."s",$blue);
                }
            }
            $lineIndex=0;
			//imagelinethick($im, $i*$scal*(1000/150)+$offsetX, (-((1)/0.1)*(1000/150)*6+($lineHeight*$y))*$scal+$offsetY, ($i+1)*$scal*(1000/150)+$offsetX, (-((1)/0.1)*(1000/150)*6+($lineHeight*$y))*$scal+$offsetY, $black,2);
            //imagelinethick($im, $i*$scal*(1000/150)+$offsetX, (-((((0)*2.52)/416)/0.1)*(1000/150)*6+($lineHeight*$y))*$scal+$offsetY, ($i+1)*$scal*(1000/150)+$offsetX, (-((((0)*2.52)/416)/0.1)*(1000/150)*6+($lineHeight*$y))*$scal+$offsetY, $black,2);
            imagelinethick($im, $i*$scal*(1000/150)+$offsetX, (-(((($data[$i+(1500*$y)]-2048)*2.52)/416)/0.1)*(1000/150)*6+($lineHeight*$y))*$scal+$offsetY, ($i+1)*$scal*(1000/150)+$offsetX, (-(((($data[$i+1+(1500*$y)]-2048)*2.52)/416)/0.1)*(1000/150)*6+($lineHeight*$y))*$scal+$offsetY, $black,2);
        }
    }
    ImagePng($im,$存放目录.'/'.$生成图片名称);
    ImageDestroy($im);
    return true;
}


function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    /* 下面两行只在线段直角相交时好使
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
    */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}



/*
* 钉钉推送消息
*/

function 钉钉推送($userid, $内容){

	require_once(APPPATH . "libraries/DingTalk.php");

	$钉钉 = new DingTalk();
	$data['touser'] = $userid;
	$data['toparty'] = '';
	$data['agentid'] = AGENTID;
	$data['msgtype'] = 'text';
	$data['text']['content'] = $内容;
	$json数据 = json_encode($data);
	$返回信息 = $钉钉->send_message($json数据);
	return $返回信息;

}


/*
* 康泰心电仪采样数据转换 为 理邦的采样数据格式
*/
function 康泰心电仪采样数据转换($数据){

	$心电图 = explode(";", $数据);
	$心电采样 = array();
	$心电采样['MDC_ECG_HEART_RATE']    	= (int)$心电图[0];
	$心电采样['MDC_ECG_TIME_PD_PR']  	= $心电图[1];
	$心电采样['MDC_ECG_TIME_PD_P']   	= $心电图[2];
	$心电采样['MDC_ECG_TIME_PD_QRS'] 	= $心电图[3];
	$心电采样['MDC_ECG_ANGLE_T_FRONT']   = $心电图[4];
	$心电采样['MDC_ECG_TIME_PD_QT']  	= $心电图[5];
	$心电采样['MDC_ECG_TIME_PD_QTc'] 	= $心电图[6];
	$心电采样['MDC_ECG_ANGLE_P_FRONT']   = $心电图[7];
	$心电采样['MDC_ECG_ANGLE_QRS_FRONT'] = $心电图[8];
	$心电采样['MDC_ECG_ANGLE_T_FRONT']   = $心电图[9];
	$心电采样['EDAN_RV5']     			= $心电图[10];
	$心电采样['EDAN_SV1']     			= $心电图[11];
	return $心电采样;
}
