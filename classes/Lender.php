<?php

class SN_Lead_Manager_Lender {

	protected $_lender;

	protected $_meta;

	public function __construct($lender) {
		$this->_lender = $lender;
		$this->_meta = array();
	}

	public function getLogo($nossl = false) {
		$logo = $this->_get('lender_logo');
		if($nossl && strpos($logo, 'http://') === false)
			$logo = 'http:'.$logo;
		elseif(!$nossl && strpos($logo, 'http://') !== false)
			$logo = str_replace('http://', '//', $logo);
		return $logo;
	}

	public function getSlug() {
		return $this->_get('lender_slug');
	}

	public function getThankyouDescription() {
		return $this->_get('lender_ty_description');
	}

	public function getCTA() {
		return $this->_get('lender_ty_cta');
	}

	public function getCTASubtitle() {
		return $this->_get('lender_ty_cta_subtitle');
	}

	public function getCTALink() {
		return $this->_get('lender_ty_cta_link');
	}

	public function getWhatsNextDescription() {
		return $this->_get('lender_whatsnext_description');
	}

	public function getNextSteps() {
		return $this->_get('lender_whatsnext');
	}

	protected function _get($meta_key) {
		if(!isset($this->_meta[$meta_key]))
			$this->_meta[$meta_key] = get_post_meta($this->ID, $meta_key, true);

		return $this->_meta[$meta_key];
	}

	public function __get($var) {
		return $this->_lender->$var;
	}

}