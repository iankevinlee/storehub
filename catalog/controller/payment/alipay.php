<?php
class ControllerPaymentAlipay extends Controller {
	protected function index() {
		$alipay_config = array();
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		$alipay_config['input_charset'] = strtolower('utf-8');
		$alipay_config['sign_type'] = strtoupper('MD5');
		$alipay_config['cacert'] = getcwd().'\\cacert.pem';
		$alipay_config['transport'] = "http";
		
		$alipay_config['seller_email'] = $this->config->get('alipay_account');
		$alipay_config['partner'] = $this->config->get('alipay_merchant');
		$alipay_config['key'] = $this->config->get('alipay_signature');
		
		$alipay_config['alipay_gateway_new'] = "https://mapi.alipay.com/gateway.do?";
		$this->data['method']="get";
		$this->data['action'] = $alipay_config['alipay_gateway_new']."_input_charset=".trim(strtolower($alipay_config['input_charset']));
		
		
		$alipay_config['payment_type'] = "1";
		//$alipay_config['notify_url'] = $this->url->link('payment/alipay/callback', '', 'SSL');
		//$alipay_config['return_url'] = $this->url->link('checkout/success');
		$alipay_config['notify_url'] = $this->config->get('alipay_callback_base_url').'catalog/controller/payment/alipay_notify.php';
		$alipay_config['return_url'] = $this->config->get('alipay_callback_base_url').'catalog/controller/payment/alipay_return.php';
		//$alipay_config['cancel_return'] = $this->url->link('checkout/checkout', '', 'SSL');
		$alipay_config['quantity'] = "1";
		$alipay_config['logistics_fee']="0.00";
		$alipay_config['logistics_type'] = 'EXPRESS';
		$alipay_config['logistics_payment'] = 'BUYER_PAY';
		$alipay_config['body']= "";
		$alipay_config['show_url'] = "";
		
		
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$alipay_config['receive_name'] = html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8');
		$alipay_config['receive_address'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');
		$alipay_config['receive_zip'] = html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
		$alipay_config['receive_phone'] = "";
		$alipay_config['receive_mobile'] = html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');
		$alipay_config['out_trade_no'] = html_entity_decode($order_info['order_id'], ENT_QUOTES, 'UTF-8');
		$alipay_config['price'] = round(html_entity_decode($order_info['total'], ENT_QUOTES, 'UTF-8'),2);
		
		$alipay_config['subject'] = "订单流水号:".$alipay_config['out_trade_no'] ;
		
		
		$parameter = array(
		"service" => "trade_create_by_buyer",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> $alipay_config['notify_url'],
		"return_url"	=> $alipay_config['return_url'],
		"seller_email"	=> trim($alipay_config['seller_email']),
		"out_trade_no"	=> $alipay_config['out_trade_no'],
		"subject"	=> $alipay_config['subject'],
		"price"	=> $alipay_config['price'] ,
		//"price"	=> "0.01",
		"quantity"	=> $alipay_config['quantity'],
		"logistics_fee"	=> $alipay_config['logistics_fee'],
		"logistics_type"	=> $alipay_config['logistics_type'],
		"logistics_payment"	=> $alipay_config['logistics_payment'],
		"body"	=> $alipay_config['body'],
		"show_url"	=> $alipay_config['show_url'],
		"receive_name"	=> $alipay_config['receive_name'],
		"receive_address"	=> $alipay_config['receive_address'],
		"receive_zip"	=> $alipay_config['receive_zip'],
		"receive_phone"	=> $alipay_config['receive_phone'],
		"receive_mobile"	=> $alipay_config['receive_mobile'],
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		$this->data['para'] = $parameter;
		
		//alipay trade manage
		$this->load->model('payment/alipay');
		$this->model_payment_alipay->init($alipay_config);
		$this->data['para']=$this->model_payment_alipay->buildRequestPara($parameter);
				
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/alipay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/alipay.tpl';
		} else {
			$this->template = 'default/template/payment/alipay.tpl';
		}	
		
		$this->render();
	}
	
	public function returnManage()
	{
		//alipay return transaction manage
		
	}

	public function callback() {
		
		if (isset($this->request->post['out_trade_no'])) {
			$order_id = $this->request->post['out_trade_no'];
		} else {
			$order_id = 0;
		}
		
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if($order_info)
		{	
			$this->load->model('payment/alipay');
			$alipay_config['partner'] = $this->config->get('alipay_merchant');
			$alipay_config['key'] = $this->config->get('alipay_signature');
			$alipay_config['cacert'] = getcwd().'\\cacert.pem';
			$alipay_config['transport'] = "https";
			
			$this->model_payment_alipay->init($alipay_config);
			
			$verify_result = $this->model_payment_alipay->verifyNotify();
					
			if($verify_result)
			{
				//verify success
				
				//alipay trade status
				$trade_status = $this->request->post['trade_status'];
		
		
				if($trade_status == 'WAIT_BUYER_PAY') 
				{
					$order_status_id = $this->config->get("alipay_wait_buyer_pay_status_id");
		    	}
				else if($trade_status == 'WAIT_SELLER_SEND_GOODS') 
				{
					$order_status_id = $this->config->get("alipay_wait_seller_send_goods_status_id");
		    	}
				else if($trade_status == 'WAIT_BUYER_CONFIRM_GOODS') 
				{
					$order_status_id = $this->config->get("alipay_wait_buyer_confirm_goods_status_id");
		   		}
				else if($trade_status == 'TRADE_FINISHED') 
				{
					$order_status_id = $this->config->get("alipay_trade_finished_status_id");
		   		}
		    	else 
		    	{
					$order_status_id = $this->config->get("alipay_trade_error_status_id");
		    	}			
			}
			else
			{
				if($this->request->post['trade_status'] == 'TRADE_CLOSED')
				{
					$order_status_id = $this->config->get("alipay_trade_closed_status_id");
				}
				else 
				{
					$order_status_id = $this->config->get("alipay_trade_error_status_id");
				}
			}
			
					
			if (!$order_info['order_status_id']) 
			{
				$this->model_checkout_order->confirm($order_id, $order_status_id);
			} 
			else 
			{
				$this->model_checkout_order->update($order_id, $order_status_id);
			}
			
		}
	}
}
?>