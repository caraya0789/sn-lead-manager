<?php

class SN_Lead_Manager_Providers {

	protected static $_instance;

	public function registerCPT() {
		register_post_type('providers', array(
			'public' => true,
			'labels' => array(
                'name' => 'Providers',
                'singular_name' => 'Provider'
            ),
            'show_in_menu' => 'sn_lead_manager_admin',
            'supports' => array('title'),
            'exclude_from_search' => true,
            'publicly_queryable' => false
		));
	}

	public function createProviderFields() {
		$cmb = new_cmb2_box( array(
			'id'            => 'providers_metabox',
			'title'         => 'Provider Data',
			'object_types'  => array( 'providers' )
		));

		$cmb->add_field( array(
			'name' => 'Title Suffix',
			'id'   => 'provider_title_suffix',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Slug',
			'id'   => 'provider_slug',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Filter Set ID',
			'id'   => 'provider_filter',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'LC Campaign ID',
			'id'   => 'provider_campaign_id',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Phone',
			'id'   => 'provider_phone',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Logo',
			'id'   => 'provider_logo',
			'type' => 'file'
		));

		$cmb->add_field( array(
			'name' => 'Liscence Text',
			'id'   => 'provider_liscence',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Show Email',
			'desc' => 'Show Email Button',
			'id'   => 'provider_show_email',
			'type' => 'checkbox'
		));

		$cmb->add_field( array(
			'name' => 'Email',
			'id'   => 'provider_email',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Show Website',
			'desc' => 'Show Visit Website Button',
			'id'   => 'provider_show_website',
			'type' => 'checkbox'
		));

		$cmb->add_field( array(
			'name' => 'Website',
			'id'   => 'provider_website',
			'type' => 'text_url'
		));

		// Lead Delivery settings
		$cmb_delivery = new_cmb2_box(array(
			'id'            => 'providers_delivery',
			'title'         => 'Lead Delivery Settings',
			'object_types'  => array( 'providers' ),
			'closed' 		=> false
		));

		$cmb_delivery->add_field(array(
			'name' 	=> 'Province',
			'id'	=> 'provider_delivery_province',
			'type'	=> 'multicheck',
			'options' => array(
				'AB' => '(AB) Alberta',
				'BC' => '(BC) British Columbia',
				'MB' => '(MB) Manitoba',
				'NB' => '(NB) New Brunswick',
				'NL' => '(NL) Newfoundland and Labrador, Newfoundland, Labrador',
				'NT' => '(NT) Northwest Territories',
				'NS' => '(NS) Nova Scotia',
				'NU' => '(NU) Nunavut',
				'ON' => '(ON) Ontario',
				'PE' => '(PE) Prince Edward Island',
				'QC' => '(QC) Quebec',
				'SK' => '(SK) Saskatchewan',
				'YT' => '(YT) Yukon'
			)
		));

		$cmb_delivery->add_field( array(
			'name' => 'Minimun Debt Amount',
			'id'   => 'provider_delivery_debt_min',
			'type' => 'text',
			'default' => '0'
		));

		$cmb_delivery->add_field( array(
			'name' => 'Maximun Debt Amount',
			'id'   => 'provider_delivery_debt_max',
			'type' => 'text',
			'default' => '200000'
		));

		$cmb_delivery->add_field( array(
			'name' => 'Postal Code (regex)',
			'id'   => 'provider_delivery_postal',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => 3
			)
		));

		$last_sent = get_post_meta($_GET['post'], 'provider_delivery_last_sent', true);

		$cmb_delivery->add_field( array(
			'name' => 'Last Sent',
			'id'   => 'provider_delivery_last_sent',
			'type' => 'text',
			'default' => '0',
			'desc' => empty($last_sent) ? '' : date('F j, Y - g:i:s a', $last_sent + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ))
		));

		$cmb_delivery->add_field( array(
			'name' => 'Priority',
			'id'   => 'provider_delivery_priority',
			'type' => 'select',
			'options' => array(
				'2'  => 'High',
				'1'  => 'Normal',
				'0'  => 'Low'
			)
		));

		$daily_cap = get_post_meta($_GET['post'], 'provider_daily_cap', true);

		$cmb_delivery->add_field( array(
			'name' => 'Daily Cap Reached On',
			'id'   => 'provider_daily_cap',
			'type' => 'text',
			'default' => '0',
			'desc' => empty($daily_cap) ? '' : date('F j, Y - g:i:s a', $daily_cap + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ))
		));

		$overall_cap = get_post_meta($_GET['post'], 'provider_overall_cap', true);

		$cmb_delivery->add_field( array(
			'name' => 'Overall Cap Reached On',
			'id'   => 'provider_overall_cap',
			'type' => 'text',
			'default' => '0',
			'desc' => empty($overall_cap) ? '' : date('F j, Y - g:i:s a', $overall_cap + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ))
		));

		$whatsnext = get_post_meta($_GET['post'], 'provider_whatsnext_description', true);

		$cmb_whatnext = new_cmb2_box(array(
			'id'            => 'providers_whatsnext',
			'title'         => 'What Happens Next',
			'object_types'  => array( 'providers' ),
			'closed' 		=> !$whatsnext
		));

		$cmb_whatnext->add_field(array(
			'name' 	=> 'Thank You Message',
			'id'	=> 'provider_ty_message',
			'type'	=> 'textarea',
			'attributes' => array(
				'rows' => 3
			)
		));

		$cmb_whatnext->add_field(array(
			'name' 	=> 'Thank You Message (Mobile)',
			'id'	=> 'provider_ty_message_xs',
			'type'	=> 'textarea',
			'attributes' => array(
				'rows' => 3
			)
		));

		$cmb_whatnext->add_field( array(
			'name' => "What's Next Description",
			'id'   => 'provider_whatsnext_description',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => 3
			)
		));

		$whatsnext_group = $cmb_whatnext->add_field( array(
			'id'          => 'provider_whatsnext',
			'type'        => 'group',
			'options'     => array(
				'group_title'   => 'Next Step {#}',
				'add_button'    => 'Add Step',
				'remove_button' => 'Remove Step',
				'sortable'      => true
			)
		));

		$cmb_whatnext->add_group_field($whatsnext_group, array(
			'name' => 'Title',
			'id' => 'title',
			'type' => 'text'
		));

		$cmb_whatnext->add_group_field($whatsnext_group, array(
			'name' => 'Description',
			'id' => 'description',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => 3
			)
		));
		

		$savings = get_post_meta($_GET['post'], 'provider_show_savings', true);

		$cmb_savings = new_cmb2_box(array(
			'id'            => 'providers_savings',
			'title'         => 'Savings Information',
			'object_types'  => array( 'providers' ),
			'closed' 		=> !($savings === 'on')
		));

		$cmb_savings->add_field(array(
		    'name' => 'Show Savings',
		    'id'   => 'provider_show_savings',
		    'description' => 'Show Projected Savings Tab',
		    'type' => 'checkbox'
		));

		$cmb_savings->add_field(array(
		    'name' => 'Savings Formula',
		    'id'   => 'provider_savings_formula',
		    'description' => 'Multiply amount by this value',
		    'type' => 'text'
		));

		$cmb_savings->add_field(array(
		    'name' => 'Savings Text',
		    'id'   => 'provider_savings_text',
		    'description' => 'Variables: {savings}',
		    'type' => 'text'
		));

		$cmb_savings->add_field(array(
		    'name' => 'Savings Description',
		    'id'   => 'provider_savings_description',
		    'description' => 'Variables: {savings} {amount} {payoff_time}',
		    'type' => 'textarea',
		    'attributes' => array(
		    	'rows' => 3
		    )
		));

		$cmb_savings->add_field(array(
		    'name' => 'Savings Disclaimer',
		    'id'   => 'provider_savings_disclaimer',
		    'description' => 'Variables: {savings} {amount} {payoff_time}',
		    'type' => 'textarea',
		    'attributes' => array(
		    	'rows' => 5
		    )
		));

		$cmb_savings->add_field(array(
		    'name' => 'Default Interest',
		    'id'   => 'provider_default_interest',
		    'type' => 'text',
		    'default' => '0.18'
		));

		$cmb_savings->add_field(array(
		    'name' => 'Default Minimum Payment',
		    'id'   => 'provider_default_payment',
		    'type' => 'text',
		    'default' => '0.04'
		));


		$about = get_post_meta($_GET['post'], 'provider_agent_about', true);
		$open3 = $about !== false && !empty($about);

		$cmb_agent = new_cmb2_box(array(
			'id'            => 'providers_agent',
			'title'         => 'Agent Information',
			'object_types'  => array( 'providers' ),
			'closed' 		=> !$open3
		));

		$cmb_agent->add_field( array(
			'name' => 'About Us',
			'id' => 'provider_agent_about',
			'type' => 'textarea'
		));

		$cmb_agent->add_field( array(
			'name' => 'About Us Highlights',
			'description' => 'One per line',
			'id' => 'provider_agent_about_highlights',
			'type' => 'textarea'
		));

		$cmb_agent->add_field( array(
			'name' => 'Picture',
			'id'   => 'provider_agent_picture',
			'type' => 'file'
		));

		$cmb_agent->add_field( array(
			'name' => 'Name',
			'id'   => 'provider_agent_name',
			'type' => 'text'
		));

		$cmb_agent->add_field( array(
			'name' => 'Address',
			'id'   => 'provider_agent_address',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => '2'
			)
		));

		$cmb_agent->add_field( array(
			'name' => 'Show BBB',
			'desc' => 'Agent is BBB Certified',
			'id'   => 'provider_agent_bbb',
			'type' => 'checkbox'
		));

		$cmb_agent->add_field( array(
			'name' => 'BBB Profile URL',
			'id'   => 'provider_agent_bbb_url',
			'type' => 'text_url'
		));

		$cmb_agent->add_field( array(
			'name' => 'Facebook',
			'id'   => 'provider_agent_facebook',
			'type' => 'text_url'
		));

		$cmb_agent->add_field( array(
			'name' => 'Twitter',
			'id'   => 'provider_agent_twitter',
			'type' => 'text_url'
		));


		$testimonials = get_post_meta($_GET['post'], 'provider_testimonials', true);
		$open2 = $testimonials !== false && !empty($variations);

		$cmb_testimonials = new_cmb2_box(array(
			'id'            => 'providers_testimonials_mb',
			'title'         => 'Testimonials',
			'object_types'  => array( 'providers' ),
			'closed' 		=> !$open2
		));

		$testimonials_group = $cmb_testimonials->add_field( array(
			'id'          => 'provider_testimonials',
			'type'        => 'group',
			'options'     => array(
				'group_title'   => 'Testimonial {#}',
				'add_button'    => 'Add Testimonial',
				'remove_button' => 'Remove Testimonial',
				'sortable'      => true
			)
		));

		$cmb_testimonials->add_group_field($testimonials_group, array(
			'name' => 'Text',
			'id' => 'text',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => 5
			)
		));

		$cmb_testimonials->add_group_field($testimonials_group, array(
			'name' => 'Date Time',
			'id' => 'datetime',
			'type' => 'text'
		));

		$cmb_testimonials->add_group_field($testimonials_group, array(
			'name' => 'Rating',
			'id' => 'rating',
			'type' => 'select',
			'default' => '',
			'options' => array(
				''  => '- Choose -',
				'5' => 'Excellent',
				'4' => 'Very Good',
				'3' => 'Good',
				'2' => 'Bad',
				'1' => 'Pretty Bad'
			)
		));

		$cmb_testimonials->add_group_field($testimonials_group, array(
			'name' => 'Video URL',
			'id' => 'video',
			'type' => 'text'
		));

		$cmb_testimonials->add_group_field($testimonials_group, array(
			'name' => 'Video Thumbnail',
			'id' => 'video_image',
			'type' => 'file'
		));




		$variations = get_post_meta($_GET['post'], 'provider_phone_variations', true);
		$open = $variations !== false && !empty($variations);

		$cmb_phone = new_cmb2_box( array(
			'id'            => 'providers_phone',
			'title'         => 'Phone Variations',
			'object_types'  => array( 'providers' ),
			'closed' 		=> !$open
		));

		$phone_variations = $cmb_phone->add_field( array(
			'id'   => 'provider_phone_variations',
			'type' => 'group',
			'options'     => array(
        		'group_title'   => 'Phone Variation {#}', // since version 1.1.4, {#} gets replaced by row number
		        'add_button'    => 'Add Variation',
		        'remove_button' => 'Remove Variation',
		        'sortable'      => true, // beta
		    ),
		));

		$cmb_phone->add_group_field( $phone_variations, array(
		    'name' => 'Regular Expression',
		    'id'   => 'regex',
		    'type' => 'text'
		));

		$cmb_phone->add_group_field( $phone_variations, array(
		    'name' => 'Phone Replacement',
		    'id'   => 'phone',
		    'type' => 'text'
		));



	}

	public function addColumnLabel($columns) {
		return array(
	        'cb' => '<input type="checkbox" />',
	        'title' => __('Title'),
	        'provider_slug' => 'Slug',
	        'provider_filter' => 'Filter',
	        // 'provider_campaign_id' => 'Campaign',
	        // 'provider_daily_cap' => 'Daily Cap',
	        'provider_overall_cap' => 'Overall Cap',
	        'provider_province' => 'Province',
	        'provider_postal' => 'Postal',
	        // 'provider_debt_amount' => 'Debt Amount',
	        'provider_last_sent' => 'Last Sent',
	        'date' => __('Date')
	    );
	}

	public function addColumnValue($column, $post_id) {
		switch ($column) {
	        case 'provider_slug':
	        	echo get_post_meta($post_id, 'provider_slug', true);
	            break;
           	case 'provider_filter':
	        	echo get_post_meta($post_id, 'provider_filter', true);
	            break;
	        case 'provider_campaign_id':
	        	echo get_post_meta($post_id, 'provider_campaign_id', true);
	            break;
	        case 'provider_province':
	        	$states = get_post_meta($post_id, 'provider_delivery_province', true);
	        	echo is_array($states) ? implode(' ', $states) : '';
	            break;
	        case 'provider_postal':
	        	echo get_post_meta($post_id, 'provider_delivery_postal', true);
	            break;
	        case 'provider_debt_amount':
	        	echo get_post_meta($post_id, 'provider_delivery_debt_min', true) . ' - ' . get_post_meta($post_id, 'provider_delivery_debt_max', true);
	            break;
	        case 'provider_last_sent':
	        	$last = get_post_meta($post_id, 'provider_delivery_last_sent', true);
	        	echo (empty($last)) ? '-' : date('M j, Y g:i a', $last + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ));
	            break;
	        case 'provider_daily_cap':
	        	$daily = get_post_meta($post_id, 'provider_daily_cap', true);
	        	echo (empty($daily)) ? '-' : date('M j, Y g:i a', $daily + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ));
	            break;
	        case 'provider_overall_cap':
	        	$overall = get_post_meta($post_id, 'provider_overall_cap', true);
	        	echo (empty($overall)) ? '-' : date('M j, Y g:i a', $overall + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ));
	            break;
	    }
	}

	public static function get($slug, $postalCode = '', $amount = 0, $test = false) {
		if(is_null($slug) || empty($slug))
			return;

		require_once SNLM_PATH . 'classes/Provider.php';

		$args = array(
			'post_type' => 'providers',
			'meta_key' => 'provider_slug',
			'meta_value' => $slug
		);

		if($test)
			$args['post_status'] = 'any';

		$provider = get_posts($args);

		if(count($provider))
			return new SN_Lead_Manager_Provider($provider[0], $postalCode, $amount);
	}

	public static function getByFilterSetID($filter, $postalCode = '', $amount = 0, $test = false) {
		if(is_null($filter) || empty($filter))
			return;

		require_once SNLM_PATH . 'classes/Provider.php';

		$args = array(
			'post_type' => 'providers',
			'meta_key' => 'provider_filter',
			'meta_value' => $filter
		);

		if($test)
			$args['post_status'] = 'any';

		$provider = get_posts($args);

		if(count($provider))
			return new SN_Lead_Manager_Provider($provider[0], $postalCode, $amount);
	}

	public static function instance() {
		if(null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	public function __construct() {
		add_action('init', array($this, 'registerCPT'));
		add_action('cmb2_admin_init', array( $this, 'createProviderFields'));

		add_filter('manage_providers_posts_columns', array($this, 'addColumnLabel'));
		add_action('manage_providers_posts_custom_column', array($this, 'addColumnValue'), 10, 2);
	}

}