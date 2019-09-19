<?php

if( php_sapi_name() !== 'cli' ) {
    die("Meant to be run from command line");
}

function find_wordpress_base_path() {
    $dir = dirname(__FILE__);
    do {
        //it is possible to check for other files here
        if( file_exists($dir."/wp-config.php") ) {
            return $dir;
        }
    } while( $dir = realpath("$dir/..") );
    return null;
}

define( 'BASE_PATH', find_wordpress_base_path()."/" );
define('WP_USE_THEMES', false);
global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
require(BASE_PATH . 'wp-load.php');

$providers = get_posts(array(
	'post_status' => array('publish', 'draft'),
	'post_type' => 'providers',
	'posts_per_page' => -1
));

foreach($providers as $p) {
	$priority = get_post_meta( $p->ID, 'provider_delivery_priority', true );
	$last_sent = get_post_meta( $p->ID, 'provider_delivery_last_sent', true );
	$daily_cap = get_post_meta( $p->ID, 'provider_daily_cap', true );
	$overall_cap = get_post_meta( $p->ID, 'provider_overall_cap', true );

	if(empty($priority) && $priority !== '0')
		update_post_meta( $p->ID, 'provider_delivery_priority', '1' );
	
	if(empty($last_sent) && $last_sent !== '0')
		update_post_meta( $p->ID, 'provider_delivery_last_sent', '0' );
	
	if(empty($daily_cap) && $daily_cap !== '0')
		update_post_meta( $p->ID, 'provider_daily_cap', '0' );
	
	if(empty($overall_cap) && $overall_cap !== '0')
		update_post_meta( $p->ID, 'provider_overall_cap', '0' );
	
	echo $p->ID . ' => ' . get_post_meta( $p->ID, 'provider_delivery_priority', true ) . "\n";
	echo $p->ID . ' => ' . get_post_meta( $p->ID, 'provider_delivery_last_sent', true ) . "\n";
	echo $p->ID . ' => ' . get_post_meta( $p->ID, 'provider_daily_cap', true ) . "\n";
	echo $p->ID . ' => ' . get_post_meta( $p->ID, 'provider_overall_cap', true ) . "\n";
	echo "\n";
}

echo "\nTotal: " . count($providers) . "\n\n";