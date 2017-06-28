<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class pay {


	public function order() {

		$amount = $this->input->post('amount');
		$pay_type = $this->input->post('pay_type');
		if($amount<=0){
			die('请输入金额');
		}

		if ($pay_type == 'alipay') {

			$alipay_config = array();
			require_once(APPPATH . "libraries/alipay/alipay.config.php");
			require_once(APPPATH . "libraries/alipay/lib/alipay_submit.class.php");

			//必填
			$subject = '支付宝充值';

			//商品描述，可空
			$body = '';

			//插入订单
			$this->db->trans_start();
			$data = array(
				'order_no' => uniqid() . rand(10000, 99999), //流水号
				'amount' => $amount,
				'pay_status' => '1', //待支付
				'pay_type' => $pay_type,
			);
			$this->db->insert('order', $data);
			$order_id = $this->db->insert_id();

			//唯一订单号，必填
			$order_id = sprintf("%016s", $order_id);

			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) {
				die('数据库出错');
			}
			//支付宝发起支付
			$parameter = array(
				"service" => $alipay_config['service'],
				"partner" => $alipay_config['partner'],
				"seller_id" => $alipay_config['seller_id'],
				"payment_type" => $alipay_config['payment_type'],
				"notify_url" => $alipay_config['notify_url'],
				"return_url" => $alipay_config['return_url'],
				"anti_phishing_key" => $alipay_config['anti_phishing_key'],
				"exter_invoke_ip" => $alipay_config['exter_invoke_ip'],
				"out_trade_no" => $order_id, //订单号
				"subject" => $subject,
				"total_fee" => $amount,
				"body" => $boby,
				"_input_charset" => trim(strtolower($alipay_config['input_charset']))
			);
			$alipaySubmit = new AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
			echo $html_text;
			die;
		}

	}

	public function alipay_return() {

		$alipay_config = array();
		require_once(APPPATH . "libraries/alipay/alipay.config.php");
		require_once(APPPATH . "libraries/alipay/lib/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();

		if ($verify_result) {
			//验证成功
			$out_trade_no = $this->input->get('out_trade_no');
			$trade_no = $this->input->get('trade_no');
			$trade_status = $this->input->get('trade_status');

			if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
				//订单处理
				$result = $this->callback($out_trade_no, $trade_no);
				$return['message'] = $result ? '支付成功' : '数据库出错';
			} else {
				//支付错误
				$return['message'] = "支付错误：交易状态=" . $trade_status;
			}

		} else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
			$return['message'] = "验证失败";
		}
		if ($return['message'] == '支付成功') {
			redirect(base_url() . "pay/success");
		} else {
			$this->load->view('pay/error.html', $return);
		}

	}

	public function alipay_notify() {

		$alipay_config = array();
		require_once(APPPATH . "libraries/alipay/alipay.config.php");
		require_once(APPPATH . "libraries/alipay/lib/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();

		if ($verify_result) {
			//验证成功
			$out_trade_no = $this->input->post('out_trade_no');
			$trade_no = $this->input->post('trade_no');
			$trade_status = $this->input->post('trade_status');

			if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
				//订单处理
				$this->callback($out_trade_no, $trade_no);
			}
			echo "success";
		} else {
			echo "fail";
		}

	}


	private function callback($out_trade_no, $trade_no) {
		$this->db->trans_start();
		$result = $this->db->select('id,amount,pay_status')->where('id', $out_trade_no)->get('order')->row_array();
		if ($result) {
			if ($result['pay_status'] != '2') {
				$data = array(
					'pay_status' => '2',
					'pay_no' => $trade_no,
				);
				$this->db->set("callback_time", "now()", false)->where('id', $result['编号'])->update('order', $data);
			}
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE) {
			log_message('支付成功后处理，数据库操作失败，充值记录编号=' . $out_trade_no);
			return false;
		}
		return true;
	}


}