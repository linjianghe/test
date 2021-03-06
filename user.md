1. [注册接口](#user-content-1-注册接口)
2. [登录接口](#user-content-2-登录接口)


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
    source | 必填 | 来源
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
				"name": "test"
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
		