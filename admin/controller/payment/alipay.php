<?php
//this class add by yangshengcheng@gmail.com

class ControllerPaymentAlipay extends Controller {
private $error = array(); 

	public function index() {
		$this->load->language('payment/alipay');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('alipay', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_pay'] = $this->language->get('text_pay');
		$this->data['text_card'] = $this->language->get('text_card');
		$this->data['text_direct'] = $this->language->get('text_direct');
		
		$this->data['entry_account'] = $this->language->get('entry_account');
		$this->data['entry_merchant'] = $this->language->get('entry_merchant');
		$this->data['entry_signature'] = $this->language->get('entry_signature');
		$this->data['entry_type'] = $this->language->get('entry_type');				
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_alipay_wait_buyer_pay_status'] = $this->language->get('entry_alipay_wait_buyer_pay_status');
		$this->data['entry_alipay_wait_seller_send_goods_status'] = $this->language->get('entry_alipay_wait_seller_send_goods_status');
		$this->data['entry_alipay_wait_buyer_confirm_goods_status'] = $this->language->get('entry_alipay_wait_buyer_confirm_goods_status');
		$this->data['entry_alipay_trade_finished_status'] = $this->language->get('entry_alipay_trade_finished_status');
		$this->data['entry_alipay_trade_closed_status'] = $this->language->get('entry_alipay_trade_closed_status');
		$this->data['entry_alipay_trade_error_status'] = $this->language->get('entry_alipay_trade_error_status');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_alipay_callback_base_url'] = $this->language->get('entry_alipay_callback_base_url');
		$this->data['error_alipay_callback_base_url'] = $this->language->get('error_alipay_callback_base_url');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['entry_debug'] = $this->language->get('entry_debug');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['account'])) { 
			$this->data['error_account'] = $this->error['account'];
		} else {
			$this->data['error_account'] = '';
		}
		
		if (isset($this->error['merchant'])) { 
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}
		
		if (isset($this->error['signature'])) { 
			$this->data['error_signature'] = $this->error['signature'];
		} else {
			$this->data['error_signature'] = '';
		}
		
		if (isset($this->error['type'])) { 
			$this->data['error_type'] = $this->error['type'];
		} else {
			$this->data['error_type'] = '';
		}
		
		if (isset($this->error['alipay_callback_base_url'])) { 
			$this->data['error_alipay_callback_base_url'] = $this->error['alipay_callback_base_url'];
		} else {
			$this->data['error_alipay_callback_base_url'] = '';
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/alipay', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/alipay', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['alipay_account'])) {
			$this->data['alipay_account'] = $this->request->post['alipay_account'];
		} else {
			$this->data['alipay_account'] = $this->config->get('alipay_account');
		}
		
		if (isset($this->request->post['alipay_merchant'])) {
			$this->data['alipay_merchant'] = $this->request->post['alipay_merchant'];
		} else {
			$this->data['alipay_merchant'] = $this->config->get('alipay_merchant');
		}

		if (isset($this->request->post['alipay_signature'])) {
			$this->data['alipay_signature'] = $this->request->post['alipay_signature'];
		} else {
			$this->data['alipay_signature'] = $this->config->get('alipay_signature');
		}
		
		if (isset($this->request->post['alipay_callback_base_url'])) {
			$this->data['alipay_callback_base_url'] = $this->request->post['alipay_callback_base_url'];
		} else {
			$this->data['alipay_callback_base_url'] = $this->config->get('alipay_callback_base_url');
		}

		if (isset($this->request->post['alipay_type'])) {
			$this->data['alipay_type'] = $this->request->post['alipay_type'];
		} else {
			$this->data['alipay_type'] = $this->config->get('alipay_type');
		}
		
		if (isset($this->request->post['alipay_total'])) {
			$this->data['alipay_total'] = $this->request->post['alipay_total'];
		} else {
			$this->data['alipay_total'] = $this->config->get('alipay_total'); 
		} 
				
		if (isset($this->request->post['alipay_order_status_id'])) {
			$this->data['alipay_order_status_id'] = $this->request->post['alipay_order_status_id'];
		} else {
			$this->data['alipay_order_status_id'] = $this->config->get('alipay_order_status_id'); 
		} 
		
		if (isset($this->request->post['alipay_debug'])) {
			$this->data['alipay_debug'] = $this->request->post['alipay_debug'];
		} else {
			$this->data['alipay_debug'] = $this->config->get('alipay_debug');
		}

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['alipay_geo_zone_id'])) {
			$this->data['alipay_geo_zone_id'] = $this->request->post['alipay_geo_zone_id'];
		} else {
			$this->data['alipay_geo_zone_id'] = $this->config->get('alipay_geo_zone_id'); 
		} 		
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['alipay_wait_buyer_pay_status_id'])) {
			$this->data['alipay_wait_buyer_pay_status_id'] = $this->request->post['alipay_wait_buyer_pay_status_id'];
		} else {
			$this->data['alipay_wait_buyer_pay_status_id'] = $this->config->get('alipay_wait_buyer_pay_status_id');
		}
		
		if (isset($this->request->post['alipay_wait_seller_send_goods_status_id'])) {
			$this->data['alipay_wait_seller_send_goods_status_id'] = $this->request->post['alipay_wait_seller_send_goods_status_id'];
		} else {
			$this->data['alipay_wait_seller_send_goods_status_id'] = $this->config->get('alipay_wait_seller_send_goods_status_id');
		}
		
		if (isset($this->request->post['alipay_wait_buyer_confirm_goods_status_id'])) {
			$this->data['alipay_wait_buyer_confirm_goods_status_id'] = $this->request->post['alipay_wait_buyer_confirm_goods_status_id'];
		} else {
			$this->data['alipay_wait_buyer_confirm_goods_status_id'] = $this->config->get('alipay_wait_buyer_confirm_goods_status_id');
		}
		
		if (isset($this->request->post['alipay_trade_finished_status_id'])) {
			$this->data['alipay_trade_finished_status_id'] = $this->request->post['alipay_trade_finished_status_id'];
		} else {
			$this->data['alipay_trade_finished_status_id'] = $this->config->get('alipay_trade_finished_status_id');
		}
		
		if (isset($this->request->post['alipay_trade_closed_status_id'])) {
			$this->data['alipay_trade_closed_status_id'] = $this->request->post['alipay_trade_closed_status_id'];
		} else {
			$this->data['alipay_trade_closed_status_id'] = $this->config->get('alipay_trade_closed_status_id');
		}
		
		if (isset($this->request->post['alipay_trade_error_status_id'])) {
			$this->data['alipay_trade_error_status_id'] = $this->request->post['alipay_trade_error_status_id'];
		} else {
			$this->data['alipay_trade_error_status_id'] = $this->config->get('alipay_trade_error_status_id');
		}
		
		
		if (isset($this->request->post['alipay_status'])) {
			$this->data['alipay_status'] = $this->request->post['alipay_status'];
		} else {
			$this->data['alipay_status'] = $this->config->get('alipay_status');
		}
		
		if (isset($this->request->post['alipay_sort_order'])) {
			$this->data['alipay_sort_order'] = $this->request->post['alipay_sort_order'];
		} else {
			$this->data['alipay_sort_order'] = $this->config->get('alipay_sort_order');
		}

		$this->template = 'payment/alipay.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/alipay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['alipay_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['alipay_signature']) {
			$this->error['signature'] = $this->language->get('error_signature');
		}
		
		if (!$this->request->post['alipay_callback_base_url']) {
			$this->error['alipay_callback_base_url'] = $this->language->get('error_alipay_callback_base_url');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}

}

?>