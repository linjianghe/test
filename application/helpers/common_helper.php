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

if (!function_exists('tel_preg')) {
	function tel_preg($telephone) {
		if (!preg_match('/^13\d{9}|14[57]\d{8}|15[0-35-9]\d{8}|18\d{9}|170[0-35-9]\d{7}|171[89]\d{7}|17[678]\d{8}$/', $telephone)) {
			json_return(500, '手机号格式有误');
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