<?php

function snlm_get_provider() {
	return SN_Lead_Manager::getProvider();
}

function snlm_get_lender() {
	return SN_Lead_Manager::getLender();
}

function snlm_get_admin() {
	return SN_Lead_Manager_Admin::instance();
}

function snlm_has_provider() {
	return (snlm_get_provider() !== null);
}

function snlm_get_option($option) {
	return snlm_get_admin()->getOption('page_options', 'snlm_'.$option);
}

function snlm_the_provider_slug() {
	echo snlm_get_provider_slug();
}

function snlm_the_lender_slug() {
	echo snlm_get_lender_slug();
}

function snlm_get_provider_slug() {
	return snlm_get_provider()->getSlug();
}
function snlm_get_lender_slug() {
	return snlm_get_lender()->getSlug();
}

function snlm_the_provider_liscence() {
	echo snlm_get_provider()->getLiscence();
}

function snlm_the_provider_title() {
	if(!snlm_has_provider()) {
		echo 'A <strong>Debt Specialist</strong> will call you soon to review your options.';
	} else {
		$title = snlm_get_provider()->post_title;
		$suffix = snlm_get_provider()->getTitleSlug();
		if(!$suffix)
			$suffix = 'will call you soon to review your options.';

		echo !empty($title) ? '<strong>' . $title . '</strong> ' . $suffix : 'A <strong>Debt Specialist</strong> ' . $suffix;
	}
}

function snlm_the_lender_title() {
	echo snlm_get_lender()->post_title;
}

function snlm_the_message() {
	if(snlm_has_provider())
		$message = snlm_get_provider()->getThankYouMessage();
	echo ($message) ? $message : snlm_get_option('ty_message');
}

function snlm_the_message_xs() {
	if(snlm_has_provider()) {
		$message = snlm_get_provider()->getThankYouMessageXs();
		if(!$message)
			$message = snlm_get_provider()->getThankYouMessage();
	}
	echo ($message) ? $message : snlm_get_option('ty_message_xs');
}

function snlm_the_lender_ty_text() {
	echo snlm_get_lender()->getThankyouDescription();
}

function snlm_has_logo() {
	$logo = snlm_get_provider()->getLogo();
	return !empty($logo);
}

function snlm_the_logo() {
	$logo = '<img src="'.snlm_get_provider()->getLogo().'">';
	
	if(snlm_get_provider()->showWebsite()) 
		$logo = '<a href="'.snlm_get_provider()->getWebsite().'" target="_blank" rel="nofollow">' . $logo . '</a>';
	
	echo $logo;
}

function snlm_the_lender_logo() {
	$logo = '<img src="'.snlm_get_lender()->getLogo().'">';
	$logo = '<a href="'.snlm_get_lender()->getCTALink().'" target="_blank" rel="nofollow">' . $logo . '</a>';
	echo $logo;
}

function snlm_the_phone($strip_dashes = false) {
	echo snlm_get_phone($strip_dashes);
}

function snlm_get_phone($strip_dashes = false) {
	if(!$strip_dashes)
		return snlm_get_provider()->getPhone();
	else
		return str_replace('-','', snlm_get_provider()->getPhone());
}

function snlm_show_buttons() {
	return snlm_get_provider()->showEmail() || snlm_get_provider()->showWebsite();
}

function snlm_the_buttons() {
	$buttons = '';
	if(snlm_get_provider()->showEmail())
		$buttons .= '<a href="#" data-email="'.snlm_get_provider()->getEmail().'" rel="nofollow" class="btn btn-orange btn-xs js-email-provider">Email Us</a>';
	if(snlm_get_provider()->showWebsite())
  		$buttons .= '<a href="'.snlm_get_provider()->getWebsite().'" data-provider="'.snlm_get_provider()->ID.'" target="_blank" rel="nofollow" class="btn btn-orange btn-xs js-visit-provider">Visit Website</a>';

  	echo $buttons;
}

function snlm_the_lender_button() {
	echo '<a href="'.snlm_get_lender()->getCTALink().'" rel="nofollow" class="btn btn-orange btn-lg" target="_blank">Apply Now</a>';
}

function snlm_the_next() {
	if(snlm_has_provider())
		$next = snlm_get_provider()->getWhatsNextDescription();
	echo ($next) ? $next : snlm_get_option('ty_next');
}

function snlm_the_lender_next() {
	echo snlm_get_lender()->getWhatsNextDescription();
}

function snlm_get_steps() {
	if(snlm_has_provider()) {
		$steps = snlm_get_provider()->getNextSteps();
		if(is_array($steps))
			return $steps;
	}
	
	$steps = snlm_get_option('next_steps');
	return is_array($steps) ? $steps : array();
}

function snlm_get_lender_next_steps() {
	$steps = snlm_get_lender()->getNextSteps();
	return is_array($steps) ? $steps : array();
}

function snlm_get_testimonials() {
	$testimonials = snlm_get_provider()->getAgentTestimonials();
	return is_array($testimonials) ? $testimonials : array();
}

function snlm_get_about() {
	$about = snlm_get_provider()->getAgentAbout();
	return $about && !empty($about);
}

function snlm_the_about() {
	$about = snlm_get_provider()->getAgentAbout();
	echo '<p>'.str_replace("\n", '</p><p>', $about).'</p>';
}

function snlm_get_about_checkmarks() {
	$hl = snlm_get_provider()->getAgentHightlights();
	return $hl && !empty($hl) ? explode("\n", $hl) : array();
}

function snlm_the_agent_picture() {
	$url = snlm_get_provider()->getAgentPicture();
	echo ($url) ? '<img style="width:100%; height:auto;" src="'.$url.'">' : '';
}

function snlm_the_agent_name() {
	echo snlm_get_provider()->getAgentName();
}

function snlm_the_agent_address() {
	echo nl2br(snlm_get_provider()->getAgentAddress());
}

function snlm_the_agent_has_social($network = '') {
	return snlm_get_provider()->agentHasSocial($network);
}
function snlm_the_agent_social($network = '') {
	echo snlm_get_provider()->getAgentSocial($network);
}


function snlm_is_certified() {
	return snlm_get_provider()->isCertifiedAgent();
}

function snlm_the_agent_profile_review() {
	if(snlm_is_certified())
		echo '<a href="'.snlm_get_provider()->getAgentProfile().'" target="_blank" rel="nofollow">Click for Review</a>';
}

function snlm_the_link($page) {
	$page_id = snlm_get_option($page.'_page');
	if($page == 'facebook') {
		echo $page_id;
		return;
	}

	echo get_permalink($page_id);
}

function snlm_get_offers() {
	$offers = snlm_get_option('offers');
	return is_array($offers) ? $offers : array();
}

function snlm_is_test() {
	return !empty($_GET['test']) && $_GET['test'] == 1;
}

function snlm_show_savings() {
	return snlm_is_test() && snlm_get_provider() && snlm_get_provider()->showSavings();
}

function snlm_the_amount() {
	echo '$'.number_format(snlm_get_provider()->getAmount());
}

function snlm_the_savings_formula() {
	echo (snlm_get_provider()->getSavingsFormula() * 100) . '%';
}

function snlm_the_savings() {
	$total = snlm_get_provider()->getTotalPaid();
	$amount = snlm_get_provider()->getAmount(); 
	echo '$'.number_format($total - $amount);
}

function snlm_the_savings_raw() {
	echo '$'.number_format(snlm_get_provider()->getSavings());
}

function snlm_the_savings_description() {
	echo snlm_get_provider()->getSavingsDescription();
}

function snlm_the_savings_disclaimer() {
	echo snlm_get_provider()->getSavingsDisclaimer();
}

function snlm_the_minimum_payment() {
	echo '$'.snlm_get_provider()->getMinimumPayment();
}

function snlm_the_total_paid() {
	echo '$'.number_format(snlm_get_provider()->getTotalPaid());
}

function snlm_the_current_payoff_time() {
	echo snlm_get_provider()->getCurrentPayoffTime() . ' months';
}

function snlm_the_payoff_time() {
	echo ((int) snlm_get_provider()->getPayoffTime()) . ' months';
}

function snlm_the_payoff_time_raw() {
	echo ((int) snlm_get_provider()->getPayoffTime());
}

function snlm_the_default_interest() {
	echo (snlm_get_provider()->getDefaultInterestRate() * 100) . '%';
}
function snlm_the_default_payment_rate() {
	echo (snlm_get_provider()->getMinimumPaymentRate() * 100) . '%';
}

function snlm_chart_config() {
	echo json_encode(array(
		'current' => array(
			'amount' => snlm_get_provider()->getAmount(),
			'minimun' => snlm_get_provider()->getMinimumPaymentRate(),
			'interest' => snlm_get_provider()->getDefaultInterestRate()
		),
		'new' => array( 
			'amount' => snlm_get_provider()->getAmount(),
			'payment' => snlm_get_provider()->getSavings(),
			'time' => (int) snlm_get_provider()->getPayoffTime()
		)
	));
}

function snlm_the_lender_cta() {
	echo snlm_get_lender()->getCTA();
}

function snlm_the_lender_cta_subtitle() {
	echo snlm_get_lender()->getCTASubtitle();
}