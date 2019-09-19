<?php

class SN_Lead_Manager_Stats {

	protected static $_instance;

	const TABLE_NAME = 'snlm_stats';

	const DB_UPDATE_OPTION = 'snlm_db_stats_version';

	public static function instance() {
		if(self::$_instance === null)
			self::$_instance = new self();

		return self::$_instance;
	}

	public function record($event, $provider_id) {
		global $wpdb;

		$table_name = $wpdb->prefix . SN_Lead_Manager_Stats::TABLE_NAME;

		$wpdb->insert( 
			$table_name, 
			array( 
				'event' => $event,
				'provider_id' => (int) $provider_id,
				'created' => current_time( 'mysql' )
			) 
		);
	}

	public static function getProviders() {
		$sql = "select DISTINCT wp_snlm_stats.provider_id, 
					wp_posts.post_title,
					wp_postmeta.meta_value
				from wp_snlm_stats
					left join wp_posts on wp_posts.ID = wp_snlm_stats.provider_id
					left join wp_postmeta on wp_postmeta.post_id = wp_snlm_stats.provider_id and wp_postmeta.meta_key = 'provider_slug'";

		global $wpdb;

		$results = $wpdb->get_results($sql);

		$today = date('Y-m-d');
		$month = date('Y-m');

		foreach($results as &$p) {
			$pid = $p->provider_id;

			$sql2 = "select * from
					  (SELECT count(*) as 'emails_today' from wp_snlm_stats WHERE created LIKE '$today%' AND event = 'email' AND provider_id = $pid) as date0,
					  (SELECT count(*) as 'website_today' from wp_snlm_stats WHERE created LIKE '$today%' AND event = 'website' AND provider_id = $pid) as date1,
					  (SELECT count(*) as 'emails_this_month' from wp_snlm_stats WHERE created LIKE '$month%' AND event = 'email' AND provider_id = $pid) as date2,
					  (SELECT count(*) as 'website_this_month' from wp_snlm_stats WHERE created LIKE '$month%' AND event = 'website' AND provider_id = $pid) as date3,
					  (SELECT count(*) as 'emails_overall' from wp_snlm_stats WHERE event = 'email' AND provider_id = $pid) as date4,
					  (SELECT count(*) as 'website_overall' from wp_snlm_stats WHERE event = 'website' AND provider_id = $pid) as date5";

			$p->stats = $wpdb->get_row($sql2);
		}

		return $results;
	}

	public static function getData() {
		global $wpdb;
		$dateTime = time();
		$last30days = array(
			'labels' => array(date('M j', $dateTime)),
			'dates' => array(date('Y-m-d', $dateTime))
		);
		while(count($last30days['labels']) <= 30) {
			$dateTime = $dateTime - 86400;
			$last30days['labels'][] = date('M j', $dateTime);
			$last30days['dates'][] = date('Y-m-d', $dateTime);
		}

		$last30days['labels'] = array_reverse($last30days['labels']);
		$last30days['dates'] = array_reverse($last30days['dates']);

		$table_name = $wpdb->prefix . SN_Lead_Manager_Stats::TABLE_NAME;

		$sql = 'SELECT * FROM ';
		$sql2 = 'SELECT * FROM ';
		foreach($last30days['dates'] as $k => $date) {
			$sql .= "(SELECT count(*) as '$date' from $table_name WHERE created LIKE '$date%' AND event = 'email') as date$k";
			$sql2 .= "(SELECT count(*) as '$date' from $table_name WHERE created LIKE '$date%' AND event = 'website') as date$k";
			if($k < (count($last30days['dates']) - 1)) {
				$sql .= ", ";
				$sql2 .= ", ";
			}
		}

		$emailStats = $wpdb->get_row( $sql, 'ARRAY_N' );
		$websiteStats = $wpdb->get_row( $sql2, 'ARRAY_N' );

		$data = array(
			'labels' => $last30days['labels'],
			'datasets' => array(
				array(
					'label' => "Emails",
					'fill' => false,
					'lineTension' => 0.1,
					'backgroundColor' => "rgba(255,144,46,0.4)",
					'borderColor' => "rgba(255,144,46,1)",
					'borderCapStyle' => 'butt',
					'borderDash' => array(),
					'borderDashOffset' => 0.0,
					'borderJoinStyle' => 'miter',
					'pointBorderColor' => "rgba(255,144,46,1)",
					'pointBackgroundColor' => "#fff",
					'pointBorderWidth' => 1,
					'pointHoverRadius' => 5,
					'pointHoverBackgroundColor' => "rgba(255,144,46,1)",
					'pointHoverBorderColor' => "rgba(220,220,220,1)",
					'pointHoverBorderWidth' => 2,
					'pointRadius' => 1,
					'pointHitRadius' => 10,
					'data' => $emailStats
				),
				array(
					'label' => "Website Clicks",
					'fill' => false,
					'lineTension' => 0.1,
					'backgroundColor' => "rgba(46,144,255,0.4)",
					'borderColor' => "rgba(46,144,255,1)",
					'borderCapStyle' => 'butt',
					'borderDash' => array(),
					'borderDashOffset' => 0.0,
					'borderJoinStyle' => 'miter',
					'pointBorderColor' => "rgba(46,144,255,1)",
					'pointBackgroundColor' => "#fff",
					'pointBorderWidth' => 1,
					'pointHoverRadius' => 5,
					'pointHoverBackgroundColor' => "rgba(46,144,255,1)",
					'pointHoverBorderColor' => "rgba(220,220,220,1)",
					'pointHoverBorderWidth' => 2,
					'pointRadius' => 1,
					'pointHitRadius' => 10,
					'data' => $websiteStats
				)
			)
		);

		echo json_encode($data);
	}

}