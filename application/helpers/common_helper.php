<?php
if (!function_exists('json_return')) {

	function json_return($code, $message = "", $data = array()) {
		$result = array(
			'code' => $code,
			'message' => $message,
			'data' => $data
		);
		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		exit(0);
	}

}

if (!function_exists('account_preg')) {
	function account_preg($account) {
		if (!preg_match("/^[\w~!@#$%^&*()]{6,20}$/", $account)) {
			json_return(500, '账号格式有误');
		}
		return true;
	}
}

if (!function_exists('tel_preg')) {
	function tel_preg($telephone) {
		if (!preg_match('/^13\d{9}|14[57]\d{8}|15[0-35-9]\d{8}|18\d{9}|170[0-35-9]\d{7}|171[89]\d{7}|17[678]\d{8}$/', $telephone)) {
			json_return(500, '手机号格式有误');
		}
		return true;
	}
}

if (!function_exists('email_preg')) {
	function email_preg($email) {
		if (!preg_match('/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/', $email)) {
			json_return(500, '邮箱格式有误');
		}
		return true;
	}
}

if (!function_exists('password_preg')) {
	function password_preg($password) {
		if (!preg_match("/^[\w~!@#$%^&*()]{6,20}$/", $password)) {
			json_return(500, '密码格式有误，请输入6-20位字符的密码');
		}
		return true;
	}
}

if (!function_exists('password_md')) {

	/**
	 * 明文密码加密
	 *
	 * @return string 加密后的密码，长度32字节
	 */
	function password_md($password) {
		$random_code = md5(rand(1, pow(2, 31) - 1) . time());
		$random_code_cut = substr($random_code, 0, 8);
		$md_24 = substr(md5($password . $random_code_cut), 8);
		return $random_code_cut . $md_24;
	}

}

if (!function_exists('password_check')) {

	/**
	 * 密码验证
	 *
	 * @return boolean 验证通过返回true，失败返回false
	 */
	function password_check($password_md, $password) {
		$random_code = substr($password_md, 0, 8);
		$old_md_24 = substr($password_md, 8);
		$md_24 = substr(md5($password . $random_code), 8);

		if ($old_md_24 == $md_24) {
			return true;
		} else {
			return false;
		}
	}

}

if (!function_exists('password_check')) {

	function post_curl($url, $data) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

}


/*
* 创建多级子目录
*/
if (!function_exists('mkdirs')) {
	function mkdirs($dir) {
		return is_dir($dir) or (mkdirs(dirname($dir)) and mkdir($dir, 0777));
	}
}

/*
* @参数: $title，string
* @参数: $content，string
* @参数: $sendto，string (支持多个以数组形式array())
*/
function send_email($title, $content, $sendto) {
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
	$Email = new CI_Email();
	$Email->initialize($config);
	$Email->from(SMTP_USER, '邮件发送');
	$Email->to($sendto);

	$Email->subject($title);
	$Email->message($content);
	$return = $Email->send();
	if ($return) {
		return '邮件已发送';
	} else {
		return $Email->print_debugger(array('headers'));
	}
}

if (!function_exists('is_phone')) {
	/*
	 * 判断是否手机号码
	 * @参数：手机号
	 * @返回值: boolean布尔型，true=是，false=否
	 * */
	function is_phone($phone) {
		if (!preg_match('/^1\d{10}$/', $phone)) {
			return false;
		}
		$operator = is_phone_operator($phone);
		if (!in_array($operator, array(1, 2, 3))) {    //不是三大运营商的手机号码
			return false;
		}
		return true;
	}
}

if (!function_exists('is_phone_operator')) {
	/**
	 * 判断手机号的类型
	 * 更新时间 2016-11-07
	 * @param $mobile
	 * @return int  1:电信 2:移动 3:联通 0:未知
	 */
	function is_phone_operator($phone) {

		$segment = substr($phone, 0, 3);
		$segment = in_array($segment, array('170', '171')) ? substr($phone, 0, 4) : $segment;
		switch ($segment) {
			case 133:
			case 153:
			case 1700:
			case 1701:
			case 1702:
			case 177:
			case 180:
			case 181:
			case 189:
			case 173:
				return 1;   //电信

			case 134:
			case 135:
			case 136:
			case 137:
			case 138:
			case 139:
			case 147:
			case 150:
			case 151:
			case 152:
			case 157:
			case 158:
			case 159:
			case 1703:
			case 1705:
			case 1706:
			case 178:
			case 182:
			case 183:
			case 184:
			case 187:
			case 188:
				return 2;   //移动

			case 130:
			case 131:
			case 132:
			case 145:
			case 155:
			case 156:
			case 1707:
			case 1708:
			case 1709:
			case 1713:
			case 1718:
			case 1719:
			case 176:
			case 185:
			case 186:
				return 3;   //联通

			default :
				return 0;
		}
	}

}


/*
 * 生成32位的唯一字符串
 */
function unique_name() {
	return md5(uniqid(mt_rand(), true));
}



/*
* JPush推送，IOS、android设备（单条）
* https://www.jpush.cn/
* @参数 $platform ios，android 必填
* @参数 $addAliasid 极光绑定id 必填
* @标题 $title 弹框标题 可填
*/
function JPush($platform, $addAliasid, $title) {

	require_once(APPPATH . "libraries/JPush/JPush.php");

	$app_key = JPUSH_APP_KEY;
	$master_secret = JPUSH_APP_SECRET;

	$client = new JPush($app_key, $master_secret);

	try {
		$result = $client->push()
			->setPlatform($platform)
			->addAlias($addAliasid)
			->setNotificationAlert($title)
			->send();
		return $result->data->msg_id;
	} catch (Exception $e) {
		return $e->getmessage();
	}

}

/*
* JPush推送，自定义消息推送消息给IOS、android设备（单条）
* https://www.jpush.cn/
* @参数 $platform ios，android 必填
* @参数 $addAliasid 极光绑定id 必填
* @标题 $title 弹框标题 可填
* @标题 $content 推送数据 array 可填
* @推送数据
* @IOS_voice  warning.caf(警告),default（默认）,null（无）
* @IOS_bg 图标背景 +1（出现加一背景）,false（无背景）
*/
function JPush_custom($platform, $addAliasid, $title, $content = array(), $IOS_voice = 'warning.caf', $IOS_bg = '+1') {

	require_once(APPPATH . "libraries/JPush/JPush.php");

	$app_key = JPUSH_APP_KEY;
	$master_secret = JPUSH_APP_SECRET;

	$client = new JPush($app_key, $master_secret);
	try {
		$result = $client->push()
			->setPlatform($platform)
			->addAlias($addAliasid)
			->setNotificationAlert($title)
			->addAndroidNotification($title, '', 1, $content)
			->addIosNotification($title, $IOS_voice, $IOS_bg, true, 'iOS category', $content)
			->setOptions(100000, 3600, null, JPUSH_APP_CERT)
			->send();
		return $result->data->msg_id;
	} catch (Exception $e) {
		return $e->getmessage();
	}

}