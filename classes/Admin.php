<?php

class SN_Lead_Manager_Admin {

	protected static $_instance;

	public $settingsKey = 'snlm_settings';

	public $pageOptionsKey = 'snlm_page_options';

	public function addMenu() {
		add_menu_page("SeitzNetwork Lead Manager", "SN Lead Manager", 'manage_options', 'sn_lead_manager_admin', array($this, 'optionsPage'),'',61);
		$page_options_page = add_submenu_page("sn_lead_manager_admin", "Page Options", "Page Options", 'manage_options', 'sn_lead_manager_page_options', array($this, 'pageOptionsPage') );
		add_submenu_page("sn_lead_manager_admin", "Import Providers", "Import Providers", 'manage_options', 'sn_lead_manager_import', array($this, 'importPage') );
		add_submenu_page("sn_lead_manager_admin", "Stats", "Stats", 'manage_options', 'sn_lead_manager_stats', array($this, 'statsPage') );
		$settings_page = add_submenu_page("sn_lead_manager_admin", "SeitzNetwork Lead Manager", "Settings", 'manage_options', 'sn_lead_manager_options', array($this, 'optionsPage') );
		add_submenu_page("sn_lead_manager_admin", "Test Routing", "Test Routing", 'manage_options', 'sn_lead_manager_test_routing', array($this, 'testRouting') );
		add_submenu_page("sn_lead_manager_admin", "Inport Leads", "Inport Leads", 'manage_options', 'sn_lead_manager_inport_leads', array($this, 'inportLeads') );

		add_action( 'admin_enqueue_scripts', array($this, 'enqueueAdminScripts') );

		add_action( "admin_print_styles-{$settings_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		add_action( "admin_print_styles-{$page_options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	public function enqueueAdminScripts($hook) {
		if($hook === 'sn-lead-manager_page_sn_lead_manager_stats') {
			wp_enqueue_style('sn_lead_manager_css_stats', SNLM_URL . 'admin/assets/css/stats.css', false, SNLM_VERSION);
			wp_enqueue_script('sn_lead_manager_js_chart', SNLM_URL . 'js/Chart.min.js', array(), '2.0.2', true );
			wp_enqueue_script('sn_lead_manager_js_stats', SNLM_URL . 'admin/assets/js/stats.js', array('sn_lead_manager_js_chart'), SNLM_VERSION, true);
		}
	}

	public function optionsPage() {
		?>
		<div class="wrap">
			<h1>SN Lead Manager Settings</h1>
			<?php cmb2_metabox_form( 'snlm_settings_metabox', $this->settingsKey ); ?>
		</div>
		<?php
	}

	public function pageOptionsPage() {
		?>
		<div class="wrap">
			<h1>SN Lead Manager Page Options</h1>
			<?php cmb2_metabox_form( 'snlm_page_options_metabox', $this->pageOptionsKey ); ?>
		</div>
		<?php
	}

	public function importPage() {
		?>
		<div class="wrap">
			<h1>SN Lead Manager Import Providers</h1>
			<p>Import Providers from previous Option Tree set up</p>
			<form method="post">
				<input type="submit" name="action" value="Import" class="button-primary">
			</form>
		</div>
		<?php

		if(!$_POST) 
			return;
		
		if(!function_exists('ot_get_option')) {
			echo '<p>Option Tree is not enabled</p>';
			return;
		}

		$companies = ot_get_option('companies');
		if(!$companies) {
			echo '<p>No providers found</p>';
			return;				
		}

		$providerTitle = array(
			'4pillars' => '4pillars',
			'cccs' => 'Consolidated Credit',
			'debtsolutions' => 'Canadian Debt Solutions',
			'tdf' => 'Total Debt Fredom',
			'mnp' => 'MNP Debt',
			'mnp2' => 'MNP Debt',
			'mnp3' => 'MNP Debt',
			'debtcare' => 'Debt Care',
			'fullcircle' => 'Full Circle',
			'refresh' => 'Refresh Financial'
		);

		echo '<p>Found '.count($companies).' Providers</p>';

		foreach($companies as $company) {

			$key = $company['title'];
			if(strpos($key, '4pillars') !== false)
				$key = '4pillars';

			$provider = array(
				'post_title' => isset($providerTitle[$key]) ? $providerTitle[$key] : 'Not Title Found',
				'post_status' => 'publish',
				'post_type' => 'providers',
				'meta_input' => array(
					'provider_phone' => $company['ty_companies_phone'],
					'provider_logo' => $company['ty_companies_logo'],
					'provider_liscence' => $company['ty_companies_liscence'],
					'provider_email' => $company['email'],
					'provider_website' => $company['website'],
					'provider_slug' => $company['title']
				)
			);
			if(isset($company['show_email_btn']))
				$provider['meta_input']['provider_show_email'] = 'on';
			if(isset($company['show_website_btn']))
				$provider['meta_input']['provider_show_website'] = 'on';

			$pid = wp_insert_post($provider);

			echo '<p>Inserted correctly: Post ID = '.$pid.'</p>';
		}
	}

	public function createSettingsMetaBox() {

		add_action( "cmb2_save_options-page_fields_snlm_settings_metabox", array( $this, 'settingsNotices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => 'snlm_settings_metabox',
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->settingsKey )
			),
		));

		$cmb->add_field( array(
			'name' => 'Lead Routing',
			'description' => 'Enable Lead Routing',
			'id'   => 'snlm_lead_routing',
			'type' => 'checkbox',
			'default' => false,
		));

		$cmb->add_field( array(
			'name' => 'Lead Conduit Endpoint',
			'description' => 'Where should we post the data?',
			'id'   => 'lc_endpoint',
			'type' => 'text',
			'default' => '',
		));

		$cmb->add_field( array(
			'name' => 'Lead Conduit Campaign',
			'description' => 'Debt Landers Intake Campaign ID',
			'id'   => 'lc_intake_campaign',
			'type' => 'text',
			'default' => '',
		));

		$cmb->add_field( array(
			'name' => 'Refresh Financial Campaign',
			'description' => 'Refresh Financial Campaign ID',
			'id'   => 'lc_refresh_campaign',
			'type' => 'text',
			'default' => '',
		));

		$cmb->add_field( array(
			'name' => 'Lead Conduit Fallback Campaign',
			'description' => 'Fallback Campaign if no elegible providers are found',
			'id'   => 'lc_fallback_campaign',
			'type' => 'text',
			'default' => '',
		));

		$cmb->add_field( array(
			'name' => 'Mailchimp API Key',
			'description' => 'You Mailchimp Account API access key',
			'id'   => 'mc_api_key',
			'type' => 'text',
			'default' => '',
		));

		$cmb->add_field( array(
			'name' => 'Mailchimp List ID',
			'description' => 'Which List should we add the lead?',
			'id'   => 'mc_list_id',
			'type' => 'text',
			'default' => '',
		));

		$cmb->add_field( array(
			'name' => 'Mandrill API Key',
			'description' => 'You Mandrill Account API access key',
			'id'   => 'mandrill_key',
			'type' => 'text',
			'default' => ''
		));
	}

	public function createPageOptionsMetaBox() {

		add_action( "cmb2_save_options-page_fields_snlm_page_options_metabox", array( $this, 'pageOptionsNotices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => 'snlm_page_options_metabox',
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->pageOptionsKey )
			),
		));

		$pageOptions = array('' => '- Select One -');

		$pages = get_pages(array('sort_column' => 'post_title', 'hierarchical' => false));
		foreach($pages as $p) {
			$pageOptions[$p->ID] = $p->post_title . ' - /' . $p->post_name;
		}

		$cmb->add_field( array(
			'name' => 'Thank You Page',
			'description' => 'Where are the forms being posted to',
			'id'   => 'snlm_action_page',
			'type' => 'select',
			'options' => $pageOptions
		));

		$cmb->add_field( array(
			'name' => 'Email Confirmation Page',
			'description' => 'The page that request users to confirm their email address before proceeding',
			'id'   => 'snlm_confirm_page',
			'type' => 'select',
			'options' => $pageOptions
		));

		$cmb->add_field( array(
			'name' => 'Shortcode',
			'description' => 'Place this in the selected page content',
			'id' => 'snlm_shortcode',
			'type' => 'text',
			'default' => '[snlm_thankyou]',
			'attributes'  => array(
				'readonly' => 'readonly'
			)
		));

		$cmb->add_field( array(
			'name' => 'Calculators Page',
			'id'   => 'snlm_calculators_page',
			'type' => 'select',
			'options' => $pageOptions
		));

		$cmb->add_field( array(
			'name' => 'Questions Page',
			'id'   => 'snlm_questions_page',
			'type' => 'select',
			'options' => $pageOptions
		));

		$cmb->add_field( array(
			'name' => 'Help Page',
			'id'   => 'snlm_help_page',
			'type' => 'select',
			'options' => $pageOptions
		));

		$cmb->add_field( array(
			'name' => 'Facebook Page',
			'id'   => 'snlm_facebook_page',
			'type' => 'text'
		));

		$cmb->add_field( array(
			'name' => 'Thank you Message',
			'id' => 'snlm_ty_message',
			'type' => 'textarea',
			'default' => "We've securely passed along your information and a debt specialist will be calling you any minute now to provide you with your free and confidential savings estimate. You can also reach out to them directly at the number below."
		));

		$cmb->add_field( array(
			'name' => 'Thank you Message (Mobile)',
			'id' => 'snlm_ty_message_xs',
			'type' => 'textarea',
			'default' => "We've securely passed along your information and a debt specialist will be calling you any minute now to provide you with your free and confidential savings estimate. You can also reach out to them directly at the number above."
		));

		$cmb->add_field( array(
			'name' => 'What Happens Next?',
			'id' => 'snlm_ty_next',
			'type' => 'textarea',
			'default' => "Maecenas sed diam eget risus varius blandit sit amet non magna. Maecenas faucibus mollis interdum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum."
		));

		$steps_group = $cmb->add_field( array(
			'name'		  => 'Next Steps',
			'id'          => 'snlm_next_steps',
			'type'        => 'group',
			'options'     => array(
				'group_title'   => 'Step {#}',
				'add_button'    => 'Add Step',
				'remove_button' => 'Remove Step',
				'sortable'      => true
			)
		));

		$cmb->add_group_field($steps_group, array(
			'name' => 'Title',
			'id' => 'title',
			'type' => 'text'
		));

		$cmb->add_group_field($steps_group, array(
			'name' => 'Description',
			'id' => 'description',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => 3
			)
		));

		$offers_group = $cmb->add_field( array(
			'name'		  => 'Additional Offers',
			'id'          => 'snlm_offers',
			'type'        => 'group',
			'options'     => array(
				'group_title'   => 'Offer {#}',
				'add_button'    => 'Add Offer',
				'remove_button' => 'Remove Offer',
				'sortable'      => true
			)
		));

		$cmb->add_group_field($offers_group, array(
			'name' => 'Image',
			'id' => 'image',
			'type' => 'file'
		));

		$cmb->add_group_field($offers_group, array(
			'name' => 'Title',
			'id' => 'title',
			'type' => 'text'
		));

		$cmb->add_group_field($offers_group, array(
			'name' => 'Description',
			'id' => 'description',
			'type' => 'textarea',
			'attributes' => array(
				'rows' => '2'
			)
		));

		$cmb->add_group_field($offers_group, array(
			'name' => 'CTA',
			'id' => 'cta',
			'type' => 'text',
			'default' => 'Apply Now'
		));

		$cmb->add_group_field($offers_group, array(
			'name' => 'Link',
			'id' => 'link',
			'type' => 'text_url'
		));

	}

	public function testRouting() {
		include SNLM_PATH . 'admin/test.php';
	}

	public function settingsNotices( $object_id, $updated ) {
		if ( $object_id !== $this->settingsKey || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->settingsKey . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->settingsKey . '-notices' );
	}

	public function pageOptionsNotices( $object_id, $updated ) {
		if ( $object_id !== $this->pageOptionsKey || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->pageOptionsKey . '-notices', '', __( 'Page Options updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->pageOptionsKey . '-notices' );
	}

	public function getOption($page, $key) {
		if(!function_exists('cmb2_get_option'))
			return false;
		if($page == 'settings')
			return cmb2_get_option( $this->settingsKey, $key );
		if($page == 'page_options')
			return cmb2_get_option( $this->pageOptionsKey, $key );
	}

	public function statsPage() {
		include SNLM_PATH . 'admin/stats.php';
	}

	public function inportLeads() {
		include SNLM_PATH . 'admin/inport-leads.php';
	}

	public static function instance() {
		if(null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	public function __construct() {
		add_action('admin_menu', array($this, 'addMenu'));
		add_action('cmb2_admin_init', array($this, 'createSettingsMetaBox' ));
		add_action('cmb2_admin_init', array($this, 'createPageOptionsMetaBox' ));
	}

}