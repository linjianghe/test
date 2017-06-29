<?php

/*
*  联通物联网卡API调用类
*/

class Jasper {


    public $CI ;											//CI 对象
    public $service;										//连接对象

	private $JASPER_URI = 'http://api.jasperwireless.com/ws/schema';

	private $wsdlUrl_arr = array(
						'Terminal'	=> 'https://api.10646.cn/ws/schema/Terminal.wsdl',
						'Billing'	=> 'https://api.10646.cn/ws/schema/Billing.wsdl',			//GetTerminalUsage、GetInvoice 等用量和账单调用的 WSDL。
						'Network'	=> 'https://api.10646.cn/ws/schema/NetworkAccess.wsdl',		//GetNetworkAccessConfig、EditNetworkAccessConfig 等通信计划调用的 WSDL。
				);

	private $wsdlUrl	= 'https://api.10646.cn/ws/schema/Terminal.wsdl';		//暂时只用到这个 WSDL，有多个可用的


	/* 授权和帐号密码 */
	private $licenseKey = 'ba9c1ea9-1a97-4da6-b157-c489a0c2a681';
	private $userName	= 'lixing';
	private $password	= 'lixing123';




	/*
	* 初始化
	* @参数: $param,  数组：array('type'=>'Terminal')
	*/
    public function __construct( $param=array() ) {
        //parent::__construct();

		$this->CI = & get_instance();

		$old_error_reporting = error_reporting(0);						//因为 nusoap 会有一个 warning 信息，要屏蔽
		error_reporting($old_error_reporting && ~E_NOTICE && ~E_WARNING); 

		if (!is_array($param)) $param = array();
		$type = $param['type'];
		if ( !$type ) $type = 'Terminal';
		

		require_once( APPPATH . "libraries/nusoap-0.9.5/lib/nusoap.php" );

		$this->service = new nusoap_client($this->wsdlUrl_arr[$type], true /** wsdl **/);

		$this->service->soap_defencoding = 'UTF-8';
		$this->service->decode_utf8 = false;


		$this->service->setHeaders(
			'<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">'.
			'<wsse:UsernameToken>'.
			'<wsse:Username>'.$this->userName.'</wsse:Username>'.
			'<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->password.'</wsse:Password>'.
			'</wsse:UsernameToken>'.
			'</wsse:Security>'
		);


    }


	/*
	* 取设备列表
	* Terminal.wsdl
	*
	* 有参数时，取一定时间内已修改的终端设备
	* 无参数时，取所有设备
	* @参数：$since , int 型时间值
	*/
	public function GetModifiedTerminals( $since=0 ){
		
		$param = '';
		if ( $since ){
			$param .= '<since>' . $this->Fdate($since) . '</since>';
		}

		$res = $this->call( "GetModifiedTerminals", $param );

		if ( $res["resultcode"]==1 ){
			
			$result = $res["iccids"]["iccid"];

			if ( !is_array($result) ) {		//如果只有一条记录，也转为多维数组
				$result = array( $result );
			}
			return array(
				"resultcode" => 1,
				"result"	 => $result,
			);
		}else{
			return $res;
		}
	}


	/*
	* 取设备详细信息
	* Terminal.wsdl
	*
	* @参数：$arr , iccid 的数组
	*/
	public function GetTerminalDetails( $arr = array() ){
		
		$param = '<iccids>';
		foreach( $arr as $iccid ){
			$param .= '<iccid>' . $iccid . '</iccid>';
		}
		$param .= '</iccids>';

		$res = $this->call( "GetTerminalDetails", $param );

		if ( $res["resultcode"]==1 ){

			$result = $res['terminals']['terminal'];

			if ( count($arr) == 1 ) {		//如果只有一条记录，也转为多维数组
				$result = array( $result );
			}
			return array(
				"resultcode" => 1,
				"result"	 => $result,
			);
		}else{
			return $res;
		}
	}


	/*
	* 编辑设备状态
	* Terminal.wsdl
	*
	* @参数：$iccid, 设备iccid
	* @参数：$targetValue, 当changeType=3时，
								TEST_READY_NAME			可测试
								INVENTORY_NAME			库存
								TRIAL_NAME				试用
								ACTIVATION_READY_NAME  可激活
								ACTIVATED_NAME			已激活
								DEACTIVATED_NAME		已停用
								RETIRED_NAME			已失效
								PURGED_NAME				已清除
	* @参数：$changeType, 修改类型：3=修改SIM卡状态，4=资费计划，暂时只用到这两个，其它的参与文档
	*/
	public function EditTerminal( $iccid, $targetValue, $changeType=3 ){
		
		$param = '';
		$param .= '<iccid>'.$iccid.'</iccid>';
		$param .= '<targetValue>'.$targetValue.'</targetValue>';
		$param .= '<changeType>'.$changeType.'</changeType>';


		$res = $this->call( "EditTerminal", $param );

		if ( $res["resultcode"]==1 ){
			return $res;
		}else{
			return $res;
		}
	}


	/*
	* 返回一个或多个设备的当前会话信息(IP 地址和会话开始时间)。如果指定的设备不在线，则不返回信息。
	* Terminal.wsdl
	*
	* @参数：$iccid, 设备iccid
	*/
	public function GetSessionInfo( $iccid ){
		
		$param = '';
		$param .= '<iccid>'.$iccid.'</iccid>';

		$res = $this->call( "GetSessionInfo", $param );

		if ( $res["resultcode"]==1 ){
			return $res;
		}else{
			return $res;
		}
	}


	/*
	* 返回给定设备的当前基本资费计划和所有排队资费计划。
	* Terminal.wsdl
	*/
	public function GetTerminalRating( $iccid ){
		
		$param = '';
		$param .= '<iccid>'.$iccid.'</iccid>';

		$res = $this->call( "GetTerminalRating", $param );

		if ( $res["resultcode"]==1 ){
			return $res;
		}else{
			return $res;
		}
	}


	/*
	* 返回给定设备最近的网络注册相关信息，包括注册运营商的名称和网络节点的全球冠名地址。
	* 此类信息可以帮助您识别设备上一次成功接入网络的时间和地点，从而对网络问题进行故障诊断。
	* Terminal.wsdl
	*
	* @参数：$imsi
	*/
	public function GetTerminalLatestRegistration( $imsi ){
		
		$param = '';
		$param .= '<imsi>'.$imsi.'</imsi>';
		$param .= '';

		$res = $this->call( "GetTerminalLatestRegistration", $param );

		if ( $res["resultcode"]==1 ){
			return $res;
		}else{
			return $res;
		}
	}







	/*
	* 返回给定账户和计费周期的账单数据。
	* Billing.wsdl
	*
	* @参数：$accountId, 账户id
	* @错误：200000 计费错误
			 200100 账单不存在
			 200200 未找到设备用量

	<ns1:GetInvoiceResponse xmlns="http://api.jasperwireless.com/ws/schema" xmlns:ns1="http://api.jasperwireless.com/ws/schema">
	<ns1:correlationId>?</ns1:correlationId>
	<ns1:version>1.01</ns1:version>
	<ns1:build>jasper1.17.0-qa-070314-3877</ns1:build>
	<ns1:timestamp>2007-03-20T21:48:04.770Z</ns1:timestamp>
	<ns1:accountId>100000021</ns1:accountId>
	<ns1:invoiceId>16508</ns1:invoiceId>
	<ns1:currency>USD</ns1:currency>
	<ns1:invoiceDate>2006-11-05Z</ns1:invoiceDate>
	<ns1:dueDate>2006-12-05Z</ns1:dueDate>
	<ns1:cycleStartDate>2006-10-01Z</ns1:cycleStartDate>
	<ns1:cycleEndDate>2006-10-31Z</ns1:cycleEndDate>
	<ns1:totalTerminals>11</ns1:totalTerminals>
	<ns1:dataVolume>4.43</ns1:dataVolume>
	<ns1:subscriptionCharge>2843.05</ns1:subscriptionCharge>
	<ns1:overageCharge>22396.99</ns1:overageCharge>
	<ns1:totalCharge>25240.04</ns1:totalCharge>
	<ns1:smsVolume>300</ns1:smsVolume>
	<ns1:smsCharge>50.05</ns1:smsCharge>
	<ns1:voiceVolume>3600</ns1:voiceVolume>
	<ns1:voiceCharge>25.26</ns1:voiceCharge>
	</ns1:GetInvoiceResponse>
	*/
	public function GetInvoice( $accountId, $cycleStartDate='' ){
		
		$param = '';
		$param .= '<accountId>'.$accountId.'</accountId>';

		if( $cycleStartDate ){
			$param .= '<cycleStartDate>'.$cycleStartDate.'Z</cycleStartDate>';
		}

		$res = $this->call( "GetInvoice", $param );

		if ( $res["resultcode"]==1 ){
			return $res;
		}else{
			return $res;
		}
	}


	/*
	* 返回某个设备在特定计费周期内的流量用量。要查看该设备当前月份的用量，请使用 GetTerminalDetails 并检查MonthToDateUsage 字段。
	* Billing.wsdl
	*
	* @param: $iccid ，设备ID
	* @param: $cycleStartDate  计费周期开始日期
	*/
	public function GetTerminalUsage( $iccid, $cycleStartDate='' ){
		
		$param = '';
		$param .= '<iccid>'.$iccid.'</iccid>';
		
		if( $cycleStartDate ){
			$param .= '<cycleStartDate>'.$cycleStartDate.'Z</cycleStartDate>';
		}
		
		$res = $this->call( "GetTerminalUsage", $param );

		if ( $res["resultcode"]==1 ){
			return $res;
		}else{
			return $res;
		}
	}


	/*
	* 调用
	*/
	public function call( $request, $param ){
		
		$msg =
		'<'.$request.'Request xmlns="'.$this->JASPER_URI.'">'.
		'<messageId></messageId>'.
		'<version>1.0</version>'.
		'<licenseKey>'.$this->licenseKey.'</licenseKey>';

		if ( $param ){
			$msg .= $param;
		}
		
		$msg .= '</'.$request.'Request>';

		$result = $this->service->call($request, $msg);

		if ($this->service->fault) {

			return array(
				'resultcode'	=> -1,			//-1 失败，1为成功
				'faultcode'		=> $this->service->faultcode,
				'faultstring'	=> $this->service->faultstring,
				'faultDetail'	=> $this->service->faultDetail,
				'response'		=> $this->service->response,
			);

		  echo 'faultcode: ' . $this->service->faultcode . "\n";
		  echo 'faultstring: ' . $this->service->faultstring . "\n";
		  echo 'faultDetail: ' . $this->service->faultdetail . "\n";
		  echo 'response: ' . $this->service->response;
		  exit(0);
		}
		
		$result["resultcode"] = 1;				//-1 失败，1为成功
		return $result;
		//echo 'Response: ' . $this->service->response . "\n";
	}
	

	/*
	* 格式化日期
	* 例：2008-08-26T00:00:00Z
	*/
	private function Fdate( $time=0 ){
		return date("Y-m-d", $time) . "T" . date("H:i:s", $time) . "Z";
	}


	/*
	* 析构
	*/
	public function __destruct( ){

	}


}


	


	/*
	*
	GetSessionInfo				返回一个或多个设备的当前会话信息(IP 地址和会话开始时间)。如果指定的设备不在线，则不返回信息。
	GetAllNetworkAccessConfigs	返回所有可用的通信计划，也称为网络访问配置。

	GetNetworkAccessConfigDetails	返回给定通信计划 ID 列表的通信计划(网络访问配置)详细信息。
	GetNetworkAccessConfig			返回一个或多个设备的通信计划 ID(网络访问配置 ID)。
	EditNetworkAccessConfig			更改与给定设备相关联的通信计划(网络访问配置)。

	GetTerminalLatestRegistration

	返回给定设备最近的网络注册相关信息，包
	括注册运营商的名称和网络节点的全球冠名
	地址。此类信息可以帮助您识别设备上一次
	成功接入网络的时间和地点，从而对网络问
	题进行故障诊断。
	请注意，您的网络安装可能会影响此 API 的
	行为。如果您有任何疑问，请与您的运营商
	联系。


	资费计划：

	GetTerminalRating 返回给定设备的当前基本资费计划和所有排队资费计划。
	EditTerminalRating 向设备队列的开头添加一个资费计划。如果设备使用月付资费计划，您可以使用
	EditTerminal 立即变更资费计划。
	QueueTerminalRatePlan 向设备队列的末尾添加一个资费计划。要将计划添加到队列开头，请使用EditTerminalRating。
	RemoveRatePlanFromQueue 从设备队列中删除指定资费计划。
	ActivateTerminalEvent 为设备分配事件资费计划。
	GetTerminalEvents 返回与一个设备关联的所有事件的列表，包括历史事件、当前事件和将来的预定事件。
	GetAvailableEvents 返回给定设备可用的所有事件资费计划列表。
	DeleteTerminalEvent 为特定设备取消预定事件。您不能取消已经在进行的事件。


	用量

	GetTerminalUsage 返回某个设备在特定计费周期内的流量用量。要查看该设备当前月份的用量，请使用 GetTerminalDetails 并检查MonthToDateUsage 字段。
	GetTerminalUsageDataDetails 返回给定设备在一个特定计费周期内发生的所有流量会话的相关信息。
	GetTerminalUsageSmsDetails 返回一个设备在某个特定计费周期内发出或收到的所有短信的相关信息。
	GetTerminalUsageVoiceDetails 返回一个设备在某个特定计费周期内接到或拨打的所有通话呼叫的相关信息。


	账单：

	GetInvoice 返回给定账户和计费周期的账单数据。这些数据包括在计费 > 账单 > 账单明细页面顶部显示的详	细信息，但不包括在页面底部的各个选项卡中显示的信息。
	例如，您可以获得以下信息：
	• 总订购费用
	• 超出计划内用量的流量和/或短信的总用量费用
	• 所有计费 SIM 卡的总计费用量
	• 激活费用(如适用)
	• 网络服务费等其他费用
	您还可以使用“用量 API”，了解有关每个计费周期
	的设备用量信息。

	*/