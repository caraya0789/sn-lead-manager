<?php

class SN_Lead_Manager_Lead {

	protected static $_instance;

	public $provider;

	public $success = false;

	public $api_result = [
		'status' => 'error',
		'error' => 'Invalid Data'
	];

	public function post($leadData, $return = false) {
		if(isset($leadData['refresh_financial'])) 
			$this->postRefresh($leadData);
		elseif(isset($leadData['lead_delivered']))
			$this->provider = SN_Lead_Manager_Providers::get($leadData['lead_delivered']);
		else
			$this->postLead($leadData);

		SN_Lead_Manager::setProvider($this->provider);

		setcookie('debtca-process-lead', 1, time() + (365 * 24 * 60 * 60), '/');

		if($return)
			return $this->api_result;
	}

	public function confirm($leadData, $return = false) {
		// Validate
		if(!$this->isValid($leadData)) {
			mail('cristian@seitznetwork.com', 'Rejected Lead - Confirmation', serialize($leadData) . "\n\n" . serialize($_SERVER));
			return false;
		}
		// Normalize
		$leadData = $this->normalizeData($leadData);
		// Generate token
		$token = md5($leadData['email'] . time() . 'salt-!Debt123!');
		// Save lead data
		add_option( 'confirm-'.$token, serialize( $leadData ) );
		// Send email
		$protocol = $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
		$this->sendConfirmationEmail(
			array( 
				'email' => $leadData['email'] 
			), 
			array(
				'FNAME' => $leadData['first_name'],
				'LNAME' => $leadData['last_name'],
				'URL' => $protocol.$_SERVER['SERVER_NAME'].'/thank-you?confirm='.urlencode($token),
				'EMAIL' => $leadData['email']
			)
		);
	}

	public function postLead($leadData) {
		if(!$this->isValid($leadData)) {
			mail('cristian@seitznetwork.com', 'Rejected Lead', serialize($leadData) . "\n\n" . serialize($_SERVER));
			return false;
		}

		$leadData = $this->normalizeData($leadData);

		return $this->pingLead($leadData);
	}

	public function isValid($leadData) {
		if(empty($leadData['email']) || 
		   !preg_match('/^([a-z0-9_\.-]+\@[\da-z\.-]+\.[a-z\.]{2,6})$/i', urldecode($leadData['email']))) 
			return false;

		if(empty($leadData['postal_code']) || 
		   !preg_match('/^[a-z][0-9][a-z][0-9][a-z][0-9]$/i', $leadData['postal_code'])) 
			return false;

		if(empty($leadData['state']) || 
		   !preg_match('/^[a-z]{2}$/i', $leadData['state'])) 
			return false;

		if(empty($leadData['phone_home']) ||
			preg_match('/(\d)\1{6}$/', $leadData['phone_home']))
			return false;

		// Robots test, these should be empty, otherwise a bot filled them up, which is not a valid lead.
		if( !empty($leadData['canad1']) || !empty($leadData['canad2']) ) 
			return false;

		if( isset($leadData['g-recaptcha-response']) ) {
			if( !$this->is_valid_recaptcha($leadData['g-recaptcha-response']) ) 
				return false;			
		}

		return true;
	}

	public function is_valid_recaptcha( $token ) {
		$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
			'secret' => '6LftBXMUAAAAAFY7IPN04GKx86oJFDKor7nYTp9v',
			'response' => $token,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		]));

		$response = curl_exec($ch);
		$result = json_decode( $response, true );

		// mail('cristian@seitznetwork.com', 'Rejected GRC Token', serialize($result));

		return $result['success'];
	}

	public function normalizeData($leadData) {
		if(empty($leadData['last_name'])) {
			$names = explode(' ', trim($leadData['first_name']));
			if(count($names) > 1) {
				$leadData['first_name'] = $names[0];
				$leadData['last_name'] = $names[1];
			}
		}

		$leadData['first_name'] = ucfirst($leadData['first_name']);
		$leadData['last_name'] = ucfirst($leadData['last_name']);

		if(!is_numeric($leadData['mortgage_balance']))
			$leadData['mortgage_balance'] = 0;

		if(!is_numeric($leadData['property_value']))
			$leadData['property_value'] = 0;

		$leadData['xxEchoField'] = 'delivered';
		unset($leadData['xxRedir']);

		$leadData['email'] = preg_replace('/\.(con|conm|comn)$/', '.com', $leadData['email']);

		$leadData['postal_code'] = trim(strtoupper( str_replace( ' ', '' , $leadData['postal_code'] ) ));

		return $leadData;
	}

	public function pingLead($leadData) {
		$response = $this->_send($leadData);
		$result = $this->_parseResponse($response);

		$this->api_result = $result;

		/*echo "<!--";
		var_dump($result);
		echo "-->";*/

		if($this->_wasSuccess($result)) 
			$this->_finishPosting($result, $leadData);
	}

	public function calculate_equity($leadData) {
		if($leadData['home_owner'] != 1)
			return 0;

		$property_value = (int) $leadData['property_value'];
		$mortgage_balance = (int) $leadData['mortgage_balance'];

		if($leadData['debt_amt'] == '$100,000') {
			$debt = 100000;
		} else {
			$debt_amt = explode(' - ', $leadData['debt_amt']);
			$debt = (int) str_replace('$', '', str_replace(',', '', $debt_amt[1]) );
		}

		$equity = $property_value - ($mortgage_balance + $debt);

		return $equity;
	}

	public function calculate_raw_equity($leadData) {
		if($leadData['home_owner'] != 1)
			return 0;

		$property_value = (int) $leadData['property_value'];
		$mortgage_balance = (int) $leadData['mortgage_balance'];

		$equity = $property_value - $mortgage_balance;

		return $equity;
	}

	public function calculate_equity_utilization($leadData) {
		if($leadData['home_owner'] != 1)
			return 0;

		$equity = $this->calculate_raw_equity($leadData);
		$property_value = (int) $leadData['property_value'];
		$equity_utilization = (int) (100 - (($equity / $property_value) * 100));

		return $equity_utilization;
	}

	protected function _send($leadData, $rebuild = false) {
		$url = '';

		// Mapping data for boberdoo
		$data = array(
			'First_Name' => $leadData['first_name'],
			'Last_Name' => $leadData['last_name'],
			'Total_Debt_Amount' => $leadData['debt_amt'],
			'Phone_home' => $leadData['phone_home'],
			'Email' => $leadData['email'],
			'IP_Address' => !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'],
			'Category' => $leadData['category'],
			'Category2' => $leadData['category'],
			'keyword' => $leadData['keyword'],
			'Landing_Page' => (!empty($leadData['url'])) ? $leadData['url'] : 'https://www.debt.ca/?blank',
			'ua' => $leadData['ua'],
			'Postal_Code' => $leadData['postal_code'],
			'home_owner' => $leadData['home_owner'] == 1 ? 'Yes' : 'No',
			'property_value' => $leadData['property_value'],
			'mortgage_balance' => $leadData['mortgage_balance'],
			'Equity' => $this->calculate_equity($leadData), // Property Value - (Mortgage + Debt)
			'Equity_Raw' => $this->calculate_raw_equity($leadData), // Property Value - Mortgage
			'Equity_Utilization' => $this->calculate_equity_utilization($leadData), // Equity Raw Percentage
			'Exit' => !empty($leadData['exit']) ? $leadData['exit'] : 'No',
			'Credit_Score' => $leadData['credit_score'],
			'Credit_Score2' => $leadData['credit_score'],
			'income' => $leadData['income'],
			'device' => $leadData['device'],
			'Province' => $leadData['state'],
			'Behind_On_Bills' => 'No',
			'rebuild_credit' => (isset($leadData['build_credit']) && $leadData['build_credit'] == 'Yes') ? 'Yes' : 'No',
			'TYPE' => 19,
			'SRC' => !empty($leadData['source']) ? $leadData['source'] : 'debt-ca-Website',
			'Sub_ID' => $leadData['referer']
		);

		if($rebuild) {
			$data['Match_With_Partner_ID'] = '16';
			$data['rebuild_credit'] = 'Yes';
		}

		$params = http_build_query($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

		$response = curl_exec($ch);

		curl_close($ch);

		$this->success = true;

		return $response;
	}

	protected function _finishPosting($result, $leadData) {
		$this->setProviderFromResponse($result, $leadData['postal_code'], $leadData['amount']);
		$this->addToMailchimp($leadData);
		$this->updateNotAlone($leadData);
	}

	public function setProviderFromResponse($result, $postalCode = '', $amount = 0) {
		$this->provider = SN_Lead_Manager_Providers::getByFilterSetID($result['partners']['partner']['filter_set_id'], $postalCode, $amount);
	}

	protected function _wasSuccess($result) {
		return ($result['status'] == 'Matched' && !empty($result['partners']) && !empty($result['partners']['partner']['filter_set_id']));
	}

	protected function _parseResponse($response) {
		$resultXml = simplexml_load_string($response,'SimpleXMLElement', LIBXML_NOCDATA);
		$result = json_decode(json_encode($resultXml),TRUE);

		return $result;
	}

	public function postRefresh($leadData) {
		$leadData2 = array();
		parse_str($leadData['lead_data'], $leadData2);

		$postalCode = $leadData2['postal_code'];
		$amount = $leadData2['amount'];

		if(isset($leadData['build_credit']) && $leadData['build_credit'] == 'Yes' && $leadData['lead_delivered'] != "refresh") {
			$this->_send($leadData2, true);
		}

		$this->provider = SN_Lead_Manager_Providers::get($leadData['lead_delivered'], $postalCode, $amount);
	}

	public function addToMailchimp($leadData) {
		$email = array('email' => $leadData['email']);
		$fields = array(
			'FNAME' => $leadData['first_name'],
			'LNAME' => $leadData['last_name'],
			'IP' => !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'],
			'PHONE' => $leadData['phone_home'],
			'PROVINCE' => $leadData['state'],
			'DEBT_AMT' => $leadData['debt_amt'],
			'PROV_LOGO' => (!is_null($this->provider)) ? $this->provider->getLogo(false) : '',
			'PROV_PHONE' => (!is_null($this->provider)) ? $this->provider->getPhone($leadData['postal_code']) : '',
			'PROV_PHONE_CLEAN' => (!is_null($this->provider)) ? str_replace('-','',$this->provider->getPhone($leadData['postal_code'])) : '',
			'PROV_EMAIL' => (!is_null($this->provider) && $this->provider->showEmail()) ? $this->provider->getEmail() : '',
			'PROV_SITE' => (!is_null($this->provider) && $this->provider->showWebsite()) ? $this->provider->getWebsite() : '',
			'PROV_GST' => (!is_null($this->provider)) ? $this->provider->getLiscence() : '',
			'PROVIDER' => (!is_null($this->provider)) ? $this->provider->post_title : '',
			'TRUSTPILOT' => $this->get_trust_pilot_url( $leadData['email'], $leadData['first_name'], $leadData['last_name'] )
		);
		$this->subscribe($email, $fields);
		$this->sendWelcomeEmail($email, $fields);
	}

	public function get_trust_pilot_url($email, $fname, $lname) {
		$name = ucwords( strtolower( $fname . ' ' . $lname ) );

		$url = 'https://trustpilot.vtgr.net/api/bgl';

		$params_arr = [
			'apiKey' => '',
			'clientId' => '',
			'fullname' => $name,
			'email' => $email
		];

		$params = http_build_query($params_arr);

		$ch = curl_init($url . '?' . $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$result = json_decode( $response, true );
		if($result['Status'] == 'Success') {
			$url = str_replace('https://', '', $result['BglLink']);
			return urlencode( $url );
		}

		return '';
	}

	public function subscribe($email, $fields) {
		require_once SNLM_PATH . 'Mailchimp/Mailchimp.php';

		$mc_api_key = SN_Lead_Manager_Admin::instance()->getOption('settings', 'mc_api_key');
		$mc_list_id = SN_Lead_Manager_Admin::instance()->getOption('settings', 'mc_list_id');

		$mailchimp = new Mailchimp($mc_api_key);

		try {
			$mailchimp->lists->subscribe($mc_list_id, $email, $fields, 'html', false);
			return true;
		} catch(Exception $e) {
			return false;
		}
	}

	public function ubsubscribe($email) {
		require_once SNLM_PATH . 'Mailchimp/Mailchimp.php';

		$mc_api_key = SN_Lead_Manager_Admin::instance()->getOption('settings', 'mc_api_key');
		$mc_list_id = SN_Lead_Manager_Admin::instance()->getOption('settings', 'mc_list_id');

		$mailchimp = new Mailchimp($mc_api_key);

		try {
			$mailchimp->lists->unsubscribe($mc_list_id, array('email' => $email));
		} catch(Exception $e) {
			return false;
		}
	}

	public function testEmail($provider) {
		$email = array('email' => '');
		$email2 = array('email' => '');
		$fields = array(
			'FNAME' => 'Cristian',
			'LNAME' => 'Araya',
			'IP' => '127.0.0.1',
			'PHONE' => '204-123-1234',
			'PROVINCE' => 'SK',
			'DEBT_AMT' => '$10,000 - $14,999',
			'PROV_LOGO' => (!is_null($provider)) ? $provider->getLogo(true) : '',
			'PROV_PHONE' => (!is_null($provider)) ? $provider->getPhone() : '',
			'PROV_PHONE_CLEAN' => (!is_null($provider)) ? str_replace('-','',$provider->getPhone()) : '',
			'PROV_EMAIL' => (!is_null($provider) && $provider->showEmail()) ? $provider->getEmail() : '',
			'PROV_SITE' => (!is_null($provider) && $provider->showWebsite()) ? $provider->getWebsite() : '',
			'PROV_GST' => (!is_null($provider)) ? $provider->getLiscence() : '',
			'PROVIDER' => (!is_null($provider)) ? $provider->post_title : ''
		);
		$this->sendWelcomeEmail($email, $fields);
		$this->sendWelcomeEmail($email2, $fields);
	}

	public function sendWelcomeEmail($email, $fields) {
		require_once SNLM_PATH . 'Mandrill/Mandrill.php';
		try {
			$mandrill_key = SN_Lead_Manager_Admin::instance()->getOption('settings', 'mandrill_key');
			$mandrill = new Mandrill($mandrill_key);

			$subject = "{$fields['FNAME']}, Your Request Has Been Received";

			$to = array(
				'email' => $email['email'],
				'name' => $fields['FNAME'] . ' ' . $fields['LNAME']
			);

			$submitted_to = '';
			if(!empty($fields['PROVIDER']))
				$submitted_to = ' to <b>'.$fields['PROVIDER'].'</b>';

			$template_content = array(
				array(
					'name' => 'main_message',
					'content' => 'Your application has been submitted'.$submitted_to.'. A debt specialist will be calling you soon to review your options and provide a free debt assessment.'
				)
			);

			$merge_vars = array();
			foreach($fields as $name => $content) {
				$merge_vars[] = array(
					'name' => $name,
					'content' => $content
				);
			}

			$reply_to_email = !empty($fields['PROV_EMAIL']) ? $fields['PROV_EMAIL'] : 'no-reply@debt.ca';

			$message = array(
				'subject' => $subject,
				'from_email' => 'support@debt.ca',
				'from_name' => 'Debt.ca',
				'to' => array($to),
				'track_opens' => true,
				'track_clicks' => true,
				'signing_domain' => 'debt.ca',
				'global_merge_vars' => $merge_vars,
				'headers' => array(
					'Reply-To' => $reply_to_email
				)
			);

			$result = $mandrill->messages->sendTemplate("debt-ca-welcome", $template_content, $message, true);
			if($result[0]['status'] == 'sent')
				return true;

			return false;
		} catch(Exception $e) {
			return false;
		}

	}

	public function sendConfirmationEmail($email, $fields) {
		require_once SNLM_PATH . 'Mandrill/Mandrill.php';
		try {
			$mandrill_key = SN_Lead_Manager_Admin::instance()->getOption('settings', 'mandrill_key');
			$mandrill = new Mandrill($mandrill_key);

			$subject = "{$fields['FNAME']}, Please confirm your email";

			$to = array(
				'email' => $email['email'],
				'name' => $fields['FNAME'] . ' ' . $fields['LNAME']
			);

			$merge_vars = array();
			foreach($fields as $name => $content) {
				$merge_vars[] = array(
					'name' => $name,
					'content' => $content
				);
			}

			$reply_to_email = 'no-reply@debt.ca';

			$message = array(
				'subject' => $subject,
				'from_email' => 'support@debt.ca',
				'from_name' => 'Debt.ca',
				'to' => array($to),
				'track_opens' => true,
				'track_clicks' => true,
				'signing_domain' => 'debt.ca',
				'global_merge_vars' => $merge_vars,
				'headers' => array(
					'Reply-To' => $reply_to_email
				)
			);

			$result = $mandrill->messages->sendTemplate('email-confirmation', array(), $message, true);
			if($result[0]['status'] == 'sent')
				return true;

			return false;
		} catch(Exception $e) {
			return false;
		}

	}

	public function updateNotAlone($leadData) {
		try {
			global $wpdb;
			$last = $wpdb->get_row('Select * from not_alone_total');
			$prevAmount = 0.00;

			$amount = explode('-',$leadData['debt_amt']);
			$amount = $amount[0];

			// Format ammount
			$amount = (float) str_replace('$','',str_replace(',','',$amount));
			if($amount == 0)
				$amount = 9000;

			$name = ucfirst( $leadData['first_name'] );

			if(!empty($last)) {
				$prevAmount = $last->total;
			}

			$wpdb->replace('not_alone_total', array(
				'total' => $prevAmount + $amount,
				'id' => ($last) ? $last->id : ''
			));

			$wpdb->insert('not_alone', array(
				'name' => $name,
				'amount' => $amount,
				'state' => $leadData['state'],
				'created' => date('Y-m-d H:i:s')
			));

			$wpdb->query('delete from not_alone where id not in (select id from (select id from not_alone order by id desc limit 100) foo)');

		} catch(Exception $e) {}
	}

	public function was_success() {
		return $this->success;
	}

	public static function instance() {
		if(null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}


}