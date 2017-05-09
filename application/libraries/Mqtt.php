<?php


class Mqtt {

    public $连接状态	= false;							//是否已连接
    public $mqtt, $CI ;											//对象


	/*
	* 初始化
	* @参数: $user,  用户名
	* @参数: $pwd,   密码
	*/
    public function __construct( $param = array() ) {
        //parent::__construct();

		$this->CI = & get_instance();

		$user	= isset($param["user"]) ? $param["user"] : "";
		$pwd	= isset($param["pwd"]) ? $param["pwd"] : "";

		$ip		= $this->CI->config->item("mqtt_ip");
		$port	= $this->CI->config->item("mqtt_port");

		if ( empty($ip) ) return false;
		if ( empty($port) ) $port = 1883;
		if ( empty($user) ) $user = 'zoteri';
		if ( empty($pwd) )	$pwd = 'z123456';

		log_message("error", "MQTT connect=" . "&ip=".$ip . "&port=".$port . "&user=".$user . "&pwd=".$pwd );

		require( APPPATH . "libraries/phpMQTT.php" );
		$this->mqtt = new phpMQTT($ip, $port, "yl_server_backend");  //Change client name to something unique

		if ($this->mqtt->connect(true, null, $user, $pwd)) {

			$this->连接状态 = true;

		}else{

			//连接失败
			log_message("error", "MQTT connect fail=" . "&ip=".$ip . "&port=".$port );
			$this->连接状态 = false;
		}

    }


	/*
	* 发布
	* @参数: $topic, 主题
	* @参数: $msg,   内容
	* @返回值: 布尔型,   true 或者 false
	*/
	public function publish( $topic, $msg ){


		if ( $this->连接状态 == false ){
			log_message("error", "MQTT publish fail =" . "&topic=".$topic . "&msg=".$msg );
			return false;
		}

		$this->mqtt->publish($topic, $msg, 0);
		return true;

	}


	/*
	* 关闭
	*/
	public function close( ){

		if ( $this->连接状态 == false ){
			return false;
		}
		$this->mqtt->close();
		return true;

	}


	/*
	* 析构
	*/
	public function __destruct( ){

		if ( $this->连接状态 ){
			$this->mqtt->close();
		}
	}

	 


}
