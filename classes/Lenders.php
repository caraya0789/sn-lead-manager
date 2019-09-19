<?php

class SN_Lead_Manager_Lenders {

	protected static $_instance;

	public function registerCPT() {
		register_post_type('lenders', array(
			'public' => true,
			'labels' => array(
                'name' => 'Lenders',
                'singular_name' => 'Lender'
            ),
            'show_in_menu' => 'sn_lead_manager_admin',
            'supports' => array('title'),
            'exclude_from_search' => true,
            'publicly_queryable' => false
		));
	}

	public function createLenderFields() {
		$cmb = new_cmb2_box( array(
			'id'            => 'lenders_metabox',
			'title'         => 'Lender Data',
			'object_types'  => array( 'lenders' )
		));

		$cmb->add_field( array(
			'name' => 'Slug',
			'id'   => 'lender_slug',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Thank You Description',
			'id'   => 'lender_ty_description',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => 4
			)
		));

		$cmb->add_field( array(
			'name' => 'Logo',
			'id'   => 'lender_logo',
			'type' => 'file'
		));

		$cmb->add_field( array(
			'name' => 'CTA',
			'id'   => 'lender_ty_cta',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Thank you CTA Subtitle',
			'id'   => 'lender_ty_cta_subtitle',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Thamk you CTA Link',
			'id'   => 'lender_ty_cta_link',
			'type' => 'text_url'
		));


		$cmb_whatnext = new_cmb2_box(array(
			'id'            => 'lenders_whatsnext',
			'title'         => 'What Happens Next',
			'object_types'  => array( 'lenders' )
		));

		$cmb_whatnext->add_field( array(
			'name' => "What's Next Description",
			'id'   => 'lender_whatsnext_description',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => 3
			)
		));

		$whatsnext_group = $cmb_whatnext->add_field( array(
			'id'          => 'lender_whatsnext',
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

	}

	public function addColumnLabel($columns) {
		return array(
	        'cb' => '<input type="checkbox" />',
	        'title' => __('Title'),
	        'lenders_slug' => 'Slug',
	        'date' => __('Date')
	    );
	}

	public function addColumnValue($column, $post_id) {
		switch ($column) {
	        case 'lenders_slug':
	        	echo get_post_meta($post_id, 'lender_slug', true);
	            break;
	    }
	}

	public static function get($slug) {
		if(is_null($slug) || empty($slug))
			return;

		require_once SNLM_PATH . 'classes/Lender.php';

		$lender = get_posts(array(
			'post_type' => 'lenders',
			'meta_key' => 'lender_slug',
			'meta_value' => $slug
		));

		if(count($lender))
			return new SN_Lead_Manager_Lender($lender[0]);
	}

	public static function instance() {
		if(null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	public function __construct() {
		add_action('init', array($this, 'registerCPT'));
		add_action('cmb2_admin_init', array( $this, 'createLenderFields'));

		add_filter('manage_lenders_posts_columns', array($this, 'addColumnLabel'));
		add_action('manage_lenders_posts_custom_column', array($this, 'addColumnValue'), 10, 2);
	}

}