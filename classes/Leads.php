<?php

class SN_Lead_Manager_Leads {

	protected static $_instance;

	public function registerCPT() {
		register_post_type('leads', array(
			'public' => true,
			'labels' => array(
                'name' => 'Leads',
                'singular_name' => 'Lead'
            ),
            'show_in_menu' => 'sn_lead_manager_admin',
            'supports' => array('title'),
            'exclude_from_search' => true,
            'publicly_queryable' => false
		));
	}

	public function createLeadFields() {
		$cmb = new_cmb2_box( array(
			'id'            => 'leads_metabox',
			'title'         => 'Lead Data',
			'object_types'  => array( 'leads' )
		));

		$cmb->add_field( array(
			'name' => 'Name',
			'id'   => 'lead_name',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Delivered To',
			'id'   => 'lead_delivered_to',
			'type' => 'text'
		));
	}

	public function addColumnLabel($columns) {
		return array(
	        'cb' => '<input type="checkbox" />',
	        'title' => __('Email'),
	        'lead_name' => 'Name',
	        'lead_delivered_to' => 'Delivered To',
	        'date' => __('Date')
	    );
	}

	public function addColumnValue($column, $post_id) {
		switch ($column) {
	        case 'lead_name':
	        	echo get_post_meta($post_id, 'lead_name', true);
	            break;
	        case 'lead_delivered_to':
	        	echo get_post_meta($post_id, 'lead_delivered_to', true);
	            break;
	    }
	}

	public static function add($leadData, $provider_slug) {
		return wp_insert_post(array(
			'post_author' => 1,
			'post_title' => strtolower($leadData['email']),
			'post_type' => 'leads',
			'post_status' => 'publish',
			'meta_input' => array(
				'lead_name' => $leadData['first_name'] . ' ' . $leadData['last_name'],
				'lead_delivered_to' => $provider_slug
			)
		));
	}

	public static function getProviderSlugsFor($email) {
		if(!$email)
			return array();
		
		$leads = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => 'leads',
			'title' => strtolower($email)
		));

		if(count($leads) === 0)
			return array();

		$providers = array();
		foreach($leads as $lead) {
			$providers[] = get_post_meta($lead->ID, 'lead_delivered_to', true );
		}

		return $providers;
	}

	public static function instance() {
		if(null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	public function __construct() {
		add_action('init', array($this, 'registerCPT'));
		add_action('cmb2_admin_init', array( $this, 'createLeadFields'));

		add_filter('manage_leads_posts_columns', array($this, 'addColumnLabel'));
		add_action('manage_leads_posts_custom_column', array($this, 'addColumnValue'), 10, 2);
	}

}