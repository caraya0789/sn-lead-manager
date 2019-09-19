<?php
/*
	Plugin Name: SeitzNetwork Lead Manager
	Plugin URI:  http://seitznetwork.com
	Description: Manage lead posting
	Version:     1.0.2
	Author:      Cristian Araya
	Author URI:  http://seitznetwork.com
	Text Domain: sn_lead_manager
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'SNLM_VERSION', '1.0.2' );
define( 'SNLM__FILE__', __FILE__ );
define( 'SNLM_PATH', plugin_dir_path(SNLM__FILE__) );
define( 'SNLM_URL', plugin_dir_url(SNLM__FILE__) );

class SN_Lead_Manager {

	protected static $_instance;

	protected static $_provider;

	protected static $_lender;

	protected $_admin;

	public $isLender = false;

	protected function _includes() {

		if ( file_exists( SNLM_PATH . 'cmb2/init.php' ) ) {
			require_once SNLM_PATH . 'cmb2/init.php';
		} elseif ( file_exists( SNLM_PATH . 'CMB2/init.php' ) ) {
			require_once SNLM_PATH . 'CMB2/init.php';
		}

		require SNLM_PATH . 'classes/Admin.php';
		require SNLM_PATH . 'classes/Providers.php';
		require SNLM_PATH . 'classes/Lenders.php';
		require SNLM_PATH . 'classes/Manager.php';
		require SNLM_PATH . 'classes/Stats.php';
		require SNLM_PATH . 'classes/Leads.php';

	}

	protected function _init() {
		$this->_admin = SN_Lead_Manager_Admin::instance();
		$this->_providers = SN_Lead_Manager_Providers::instance();
		$this->_lenders = SN_Lead_Manager_Lenders::instance();
		$this->_manager = SN_Lead_Manager_Lead::instance();
		$this->_stats = SN_Lead_Manager_Stats::instance();
		$this->_leads = SN_Lead_Manager_Leads::instance();
	}

	public function hooks() {
		// Proccess post request hook
		add_action( 'parse_query', array($this, 'parseRequest') );

		// Ajax Hooks
		add_action( 'wp_ajax_debtca_submit_lead', array($this, 'processLead') );
		add_action( 'wp_ajax_nopriv_debtca_submit_lead', array($this, 'processLead') );

		add_action( 'wp_ajax_debtca_post_lead', array($this, 'postLead') );
		add_action( 'wp_ajax_nopriv_debtca_post_lead', array($this, 'postLead') );

		add_action( 'wp_ajax_debtca_ty_email', array($this, 'contactProvider') );
		add_action( 'wp_ajax_nopriv_debtca_ty_email', array($this, 'contactProvider') );
		
		add_action( 'wp_ajax_debtca_ty_website', array($this, 'registerWebsiteProvider') );
		add_action( 'wp_ajax_nopriv_debtca_ty_website', array($this, 'registerWebsiteProvider') );

		// Affiliates endpoint
		add_action( 'rest_api_init', array($this, 'registerAffilliatesEndPoint') );

		// Shortcode
		add_shortcode('snlm_thankyou', array($this, 'thankyouContent'));

		// Enqueue Scripts
		wp_register_script( 'sn_lead_manager_js_chart', SNLM_URL . 'js/Chart.min.js', array(), '2.0.2', true );
		wp_register_script( 'sn_lead_manager_js_savings', SNLM_URL . 'js/savings.js', array('sn_lead_manager_js_chart'), '1.0.2', true );

		//add_action( 'init', array($this, 'addScripts') );

		// Create Stats Table
		if ( get_option( SN_Lead_Manager_Stats::DB_UPDATE_OPTION ) !==  '1.0.0') 
	        $this->createStatsTable();
	}

	// Register Affiliate EndPoint
	public function registerAffilliatesEndPoint() {
		register_rest_route( 'leads/v1', '/post', array(
			'methods' => 'GET, POST',
			'callback' => array( $this, 'postAffilliateLead' )
		) );
	}

	// Handle Affiliate Leads
	public function postAffilliateLead() {
		if(!empty($_POST))
			$result = $this->_manager->post($_POST, true);
		elseif(!empty($_GET))
			$result = $this->_manager->post($_GET, true);
		else {
			return array(
				'status' => 'error',
				'error' => 'Wrong Request'
			);
		}

		return $result;
	}

	public function parseRequest($query) {
		$actionPage = $this->_admin->getOption('page_options', 'snlm_action_page');
		$confirmPage = $this->_admin->getOption('page_options', 'snlm_confirm_page');

		if($actionPage == $query->queried_object->ID){
			setcookie( 'hideExitPopup', 1, time() + (365 * 86400) );
			//var_dump(SN_Lead_Manager_Admin::instance()->getOption('settings', 'snlm_lead_routing')); die;
			if($_POST){
				$this->_manager->post($_POST);
			}

			elseif(isset($_GET['test']) && $_GET['test'] == 1) {
				$postalCode = !empty($_GET['postal_code']) ? $_GET['postal_code'] : '';
				$amount = !empty($_GET['amount']) ? $_GET['amount'] : 0;
				self::setProvider(SN_Lead_Manager_Providers::get($_GET['delivery'], $postalCode, $amount, true));
				if(isset($_GET['email']) && $_GET['email'] == 1) {
					$this->_manager->testEmail(self::getProvider());
				}
			} 

			elseif(isset($_GET['lender'])) {
				self::setLender(SN_Lead_Manager_Lenders::get($_GET['lender']));	
				if(self::getLender())
					$this->isLender = true;
			}

			elseif(!empty($_GET['confirm'])) {
				$leadData = unserialize( get_option('confirm-'.$_GET['confirm']) );
				if($leadData) {
					delete_option( 'confirm-'.$_GET['confirm'] );
					unset($leadData['g-recaptcha-response']);
					$this->_manager->post($leadData);
				} else {
					header('Location: /'); die;
				}
			}

			if(self::getProvider() && self::getProvider()->showSavings()) {
				wp_enqueue_script('sn_lead_manager_js_chart');
				wp_enqueue_script('sn_lead_manager_js_savings');	
			}

		} else if($query->query['pagename'] == 'unsub' && !empty($_GET['md_email'])) {
			$this->_manager->ubsubscribe(urldecode($_GET['md_email']));
		} else if($confirmPage == $query->queried_object->ID){
			setcookie( 'hideExitPopup', 1, time() + (365 * 86400) );			
			if($_POST){
				$this->_manager->confirm($_POST);
			}

		}
	}

	public function contactProvider() {
		$to = $_POST['pemail'];

		$subject = 'New Message from '.$_POST['first_name'].' '.$_POST['last_name'];
		
		$message = 'From: '.$_POST['first_name'].' '.$_POST['last_name']."\n";
		$message.= 'Email: '.$_POST['email']."\n";
		$message.= 'Phone: '.$_POST['phone1'].'-'.$_POST['phone2'].'-'.$_POST['phone3']."\n";
		$message.= 'Best time to call: '.$_POST['bttc']."\n\n";
		$message.= 'Message:'."\n";
		$message.= $_POST['message']."\n";

		$headers = 'From: Debt.ca <support@debt.ca>'."\r\n";
	    $headers.= 'Reply-To: '.$_POST['email']. "\r\n";
		
		$success = (int) wp_mail($to, $subject, $message, $headers);

		$this->_stats->record('email', $_POST['provider']);
		
		echo json_encode(array('success' => $success));
		
		wp_die();
	}

	public function registerWebsiteProvider() {
		$this->_stats->record('website', $_POST['provider']);
		echo json_encode(array('success' => 1));
		wp_die();
	}

	public function processLead() {
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');

		$this->_manager->post($_POST);

		$result = array();
		$result['success'] = 1;
		$result['lead_delivered'] = self::getProvider() !== null ? self::getProvider()->getSlug() : '';

		echo json_encode($result);
		
		wp_die(); 
	}

	public function was_success() {
		return $this->_manager->was_success();
	}

	public function postLead() {
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		header('HTTP/1.1 200 OK');

		echo $this->_manager->pingpost($_POST);
		
		wp_die(); 
	}

	public function thankyouContent() {
		require SNLM_PATH . 'template-functions.php';
		ob_start();

		if($this->isLender)
			include SNLM_PATH . 'templates/thankyou-lender.php';
		else
			include SNLM_PATH . 'templates/thankyou.php';
		$content = ob_get_clean();
		return $content;
	}

	public function createStatsTable() {
		global $wpdb;

		$table_name = $wpdb->prefix . SN_Lead_Manager_Stats::TABLE_NAME;

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(9) NOT NULL AUTO_INCREMENT,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			event varchar(100) DEFAULT '' NOT NULL,
			provider_id int(9) NOT NULL,
			UNIQUE KEY id (id),
			PRIMARY KEY (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option( SN_Lead_Manager_Stats::DB_UPDATE_OPTION, '1.0.0' );
	}

	public static function instance() {
		if(null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	public function __construct() {
		$this->_includes();
		$this->_init();
	}

	public static function setProvider($provider) {
		self::$_provider = $provider;
	}

	public static function setLender($lender) {
		self::$_lender = $lender;
	}

	public static function getProvider() {
		return self::$_provider;
	}

	public static function getLender() {
		return self::$_lender;
	}

}

function sn_lead_manager_instance() {
	return SN_Lead_Manager::instance();
}

function snlm_was_success() {
	$manager = sn_lead_manager_instance();
	return $manager->was_success();
}

add_action('plugins_loaded', array(sn_lead_manager_instance(), 'hooks'));