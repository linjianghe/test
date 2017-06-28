1. [注册接口](#user-content-1-注册接口)
2. [登录接口](#user-content-2-登录接口)
3. [未注册手机获取验证码](#user-content-3-未注册手机获取验证码)
4. [已注册手机获取验证码](#user-content-4-已注册手机获取验证码)
5. [个人中心](#user-content-5-个人中心)
6. [检查toekn是否失效](#user-content-6-检查toekn是否失效)
7. [清空当前token](#user-content-7-清空当前token)
8. [修改用户密码](#user-content-8-修改用户密码)
9. [忘记密码(找回密码)](#user-content-9-忘记密码找回密码)
10. [修改手机-姓名-头像](#user-content-10-修改手机-姓名-头像)
11. [用户反馈](#user-content-11-用户反馈)


# 1. 注册接口

* 请求链接： <https://localhost/Api/user/register>

* 传输类型： POST

* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----
    code | 必填 | 手机验证码	
    name | 必填 | 姓名
    phone | 必填 | 手机号码
    password | 必填 | 密码
    source | 必填 | 来源（ios/android）
    version | 必填 | 版本号

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10001 | 姓名不能为空
    10002 | 手机号不能为空
    10003 | 密码不能为空
    10004 | 来源不能为空(ios、andriod)
    10006 | 验证码不能为空
    10007 | 短信验证码失效
    10008 | 短信验证码错误
    10009 | 手机号码已被注册
    10010 | 数据库操作失败
    10030 | 密码格式有误，请输入6-20位字符的密码
	
* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": [
				"uid": "33",
				"headimg": "",
				"phone": "13424578897",
				"token": "9c0a12c9-c3d8-45df-b7dd-5f623f3fa139",
				"name": "林江和"
			]
		}

* 返回数据字段说明

    字段 | 说明
    --- | ---
    uid | 用户编号
    headimg | 头像
    phone | 手机号
    name | 姓名	
    token | 令牌	

# 2. 登录接口

* 请求链接： <https://localhost/Api/user/login>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    phone | 必填 | 手机号码
    password | 必填 | 密码
    source | 必填 | 来源（ios/android）
    version | 必填 | 版本号

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10002 | 手机号不能为空
    10003 | 密码不能为空
    10004 | 来源不能为空
    10024 | 账号不存在
    10028 | 密码不正确
    10030 | 密码格式有误，请输入6-20位字符的密码
	
* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": [
				"uid": "33",
				"headimg": "",
				"phone": "13424578897",
				"token": "9c0a12c9-c3d8-45df-b7dd-5f623f3fa139",
				"name": "林江和"
			]
		}

* 返回数据字段说明

    字段 | 说明
    --- | ---
    uid | 用户编号
    headimg | 头像
    phone | 手机号
    name | 姓名	
    token | 令牌	
	
# 3. 未注册手机获取验证码

* 请求链接： <https://localhost/Api/user/code>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    phone | 必填 | 手机号码
    md | 必填 | Md5(phone+加密串)

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10002 | 手机号不能为空
    10009 | 手机号码已被注册
    10010 | 数据库操作失败
    10011 | 加密串不能为空
    10012 | 加密串有误
    10013 | 今日短信验证码次数已用完
    10014 | 短信发送频繁
    10015 | 手机号码有问题
    10016 | 服务器内部错误
	
* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}

# 4. 已注册手机获取验证码

* 请求链接： <https://localhost/Api/user/sendcode>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    phone | 必填 | 手机号码
    md | 必填 | Md5(phone+加密串)

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10002 | 手机号不能为空
    10010 | 数据库操作失败
    10011 | 加密串不能为空
    10012 | 加密串有误
    10013 | 今日短信验证码次数已用完
    10014 | 短信发送频繁
    10015 | 手机号码有问题
    10016 | 服务器内部错误
    10043 | 手机号不存在
	
* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}

# 5. 个人中心

* 请求链接： <https://localhost/Api/user/index>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    token | 必填 | 登录后返回的token

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10019 | 令牌已失效

* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": [
				"uid": "33",
				"headimg": "",
				"phone": "13424578897",
				"name": "林江和"
			]
		}
		
* 返回数据字段说明

    字段 | 说明
    --- | ---
    uid | 用户编号
    headimg | 头像
    phone | 手机号
    name | 姓名	

# 6. 检查toekn是否失效

* 请求链接： <https://localhost/Api/user/tokenstatus>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    token | 必填 | 登录后返回的token
    uid | 必填 | 登录返回的用户编号

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10019 | 令牌已失效

* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}

# 7. 清空当前token

* 请求链接： <https://localhost/Api/user/tokenempty>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    token | 必填 | 登录后返回的token

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    500 | 没有数据

* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}
		
# 8. 修改用户密码

* 请求链接： <https://localhost/Api/user/resetpwd>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    token | 必填 | 登录后返回的token
    oldpassword | 必填 | 旧密码
    newpassword | 必填 | 新密码

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10003 | 密码不能为空
    10028 | 密码不正确
    10030 | 密码格式有误，请输入6-20位字符的密码
    10056 | 新旧密码不能一样

* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}	

# 9. 忘记密码(找回密码)

* 请求链接： <https://localhost/Api/user/findpwd>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    phone | 必填 | 手机号
    password | 必填 | 密码
    code | 必填 | 验证码

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10002 | 手机号不能为空
    10003 | 密码不能为空
    10006 | 验证码不能为空
    10007 | 短信验证码失效
    10008 | 短信验证码错误
    10010 | 数据库操作失败
    10024 | 账号不存在
    10030 | 密码格式有误，请输入6-20位字符的密码

* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}

# 10. 修改手机-姓名-头像#

* 请求链接： <https://localhost/Api/user/editinfo>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    token | 必填 | 登录后返回的token
    phone | 选填 | 手机号
    name | 选填 | 姓名
    headimg | 选填 | 头像
    code | 必填 | 修改手机时验证码必须填

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10058 | 上传头像出错
    10000 | 缺少参数
    10006 | 验证码不能为空
    10007 | 短信验证码失效
    10008 | 短信验证码错误
    10009 | 手机号码已被注册
    10010 | 数据库操作失败

* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}

# 11. 用户反馈

* 请求链接： <https://localhost/Api/user/feedback>

* 传输类型： POST
 
* 参数说明

    参数名称 | 是否可选 | 说明
    ---- | ---- | ----	
    token | 选填 | 登录后返回的token
    content | 必填 | 修改手机时验证码必须填

* 返回数据

    返回码 | 说明
    --- | ---
    200 | 成功
    10042 | 内容不能为空


* 返回数据示例

		{
			"code": 200,
			"message": "成功",
			"data": []
		}		