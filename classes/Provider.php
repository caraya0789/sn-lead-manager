<?php

class SN_Lead_Manager_Provider {

	protected $_provider;

	protected $_meta;

	protected $_postalCode;

	protected $_amount;

	public function __construct($provider, $postalCode, $amount) {
		$this->_provider = $provider;
		$this->_postalCode = $postalCode;
		$this->_amount = (int) $amount;
		$this->_meta = array();
	}

	public function getLogo($nossl = false) {
		$logo = $this->_get('provider_logo');
		if(!$logo || empty($logo))
			return '';
		
		if($nossl && strpos($logo, 'http://') === false && strpos($logo, 'https://') === false)
			$logo = 'http:'.$logo;
		elseif(!$nossl && strpos($logo, 'http://') !== false)
			$logo = str_replace('http://', '//', $logo);
		return $logo;
	}

	public function getPhone() {
		$phone = $this->_get('provider_phone');
		$phoneVariations = $this->_get('provider_phone_variations');
		if($phoneVariations === false || empty($phoneVariations) || !count($phoneVariations))
			return $phone;

		foreach($phoneVariations as $variation) {
			if(preg_match('/'.$variation['regex'].'/', $this->_postalCode)) 
				return $variation['phone'];
		}

		return $phone;
	}

	public function getLiscence() {
		return $this->_get('provider_liscence');
	}

	public function getTitleSlug() {
		return $this->_get('provider_title_suffix');
	}

	public function getSlug() {
		return $this->_get('provider_slug');
	}

	public function showEmail() {
		$showEmail = $this->_get('provider_show_email');
		return $showEmail === 'on';
	}

	public function getEmail() {
		return $this->_get('provider_email');
	}

	public function getWebsite() {
		return $this->_get('provider_website');
	}

	public function showWebsite() {
		$showEmail = $this->_get('provider_show_website');
		return $showEmail === 'on';
	}

	public function getAgentTestimonials() {
		return $this->_get('provider_testimonials');
	}

	public function getAgentAbout() {
		return $this->_get('provider_agent_about');
	}

	public function getAgentHightlights() {
		return $this->_get('provider_agent_about_highlights');
	}

	public function getAgentPicture($nossl = false) {
		$logo = $this->_get('provider_agent_picture');
		if($nossl && strpos($logo, 'http://') === false)
			$logo = 'http:'.$logo;
		elseif(!$nossl && strpos($logo, 'http://') !== false)
			$logo = str_replace('http://', '//', $logo);
		return $logo;
	}

	public function getAgentName() {
		return $this->_get('provider_agent_name');
	}

	public function getAgentAddress() {
		return $this->_get('provider_agent_address');
	}

	public function isCertifiedAgent() {
		$certified = $this->_get('provider_agent_bbb');
		return $certified === 'on';
	}

	public function getAgentProfile() {
		return $this->_get('provider_agent_bbb_url');
	}

	public function agentHasSocial($network = '') {
		if(!empty($network))
			return $this->_get('provider_agent_'.$network) != false && $this->_get('provider_agent_'.$network) != '';
		else
			return ($this->_get('provider_agent_facebook') != false && $this->_get('provider_agent_facebook') != '') || ($this->_get('provider_agent_twitter') != false && $this->_get('provider_agent_twitter') != '');
	}

	public function getAgentSocial($network = '') {
		return $this->_get('provider_agent_'.$network);
	}

	public function showSavings() {
		$showSavings = $this->_get('provider_show_savings');
		return $showSavings === 'on' && $this->getAmount();
	}

	public function getAmount() {
		return $this->_amount;
	}

	public function getSavingsFormula() {
		return (float) $this->_get('provider_savings_formula');
	}

	public function getSavings() {
		return $this->_amount * $this->getSavingsFormula();
	}

	public function getFormatSavings() {
		return '$'.number_format($this->getSavings());
	}

	public function getFormatAmount() {
		return '$'.number_format($this->_amount);
	}

	public function getSavingsText() {
		return str_replace('{savings}', $this->getFormatSavings(), $this->_get('provider_savings_text'));
	}

	public function getPayoffTime() {
		$amount = $this->_amount;
		$savings = $this->getSavings();

		return ($amount / $savings) + 1;
	}

	public function getSavingsDescription() {
		$description = $this->_get('provider_savings_description');
		$description = str_replace('{savings}', $this->getFormatSavings(), $description);
		$description = str_replace('{amount}', $this->getFormatAmount(), $description);
		$description = str_replace('{payoff_time}', (int) $this->getPayoffTime(), $description);
		return $description;
	}

	public function getSavingsDisclaimer() {
		return $this->_get('provider_savings_disclaimer');
	}

	public function getMinimumPaymentRate() {
		return (float) $this->_get('provider_default_payment');
	}

	public function getMinimumPayment() {
		return $this->_amount * $this->getMinimumPaymentRate();
	}

	public function getDefaultInterestRate() {
		return (float) $this->_get('provider_default_interest');
	}

	protected function _calculateDebt() {
		$payment_rate = $this->getMinimumPaymentRate();
		$interest_rate = $this->getDefaultInterestRate();
		$rate = $interest_rate / 12;

		$balance = $this->_amount;

		$months = 0;
		$interest_paid = 0;

		while($balance > 0) {
			$payment = $balance * $payment_rate;
			if($payment < 15)
				$payment = 15;

			$interest = ($balance * $rate);
			$interest_paid += $interest;

			$balance = ($balance + $interest) - $payment;
			$months++;
		}

		return array(
			'months' => $months,
			'total_paid' => $this->_amount + $interest_paid
		);
	}

	public function getTotalPaid() {
		$result = $this->_calculateDebt();
		return $result['total_paid'];
	}

	public function getCurrentPayoffTime() {
		$result = $this->_calculateDebt();
		return $result['months'];

		/**

		$type = 0;
		$future = 0;

		// Use monthly rate
		$interest = (float) $this->_get('provider_default_interest');
		$rate = $interest / 12;

		if($rate === 0) 
			return (int) ($this->_amount / $payment) + 1;

		// Payment should be negative;
		$payment = 0-$payment;
		$present = $this->_amount;

		// Return number of periods
		$num = $payment * (1 + $rate * $type) - $future * $rate;
		$den = ($present * $rate + $payment * (1 + $rate * $type));

		$result = log($num / $den) / log(1 + $rate);
		//return Math.ceil(result);
		return (int) $result + 1;

		**/
	}

	public function getWhatsNextDescription() {
		return $this->_get('provider_whatsnext_description');
	}

	public function getNextSteps() {
		return $this->_get('provider_whatsnext');
	}

	public function getThankYouMessage() {
		return $this->_get('provider_ty_message');
	}

	public function getThankYouMessageXs() {
		return $this->_get('provider_ty_message_xs');
	}

	public function updateLastSent() {
		update_post_meta($this->ID, 'provider_delivery_last_sent', time() );
	}

	protected function _get($meta_key) {
		if(!isset($this->_meta[$meta_key]))
			$this->_meta[$meta_key] = get_post_meta($this->ID, $meta_key, true);

		return $this->_meta[$meta_key];
	}

	public function __get($var) {
		return $this->_provider->$var;
	}

}