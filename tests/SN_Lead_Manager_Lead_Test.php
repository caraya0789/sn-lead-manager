<?php

use PHPUnit\Framework\TestCase;

class SN_Lead_Manager_Lead_Test extends TestCase {

	public function testEquityUtilizationCalculation() {
		$manager = SN_Lead_Manager_Lead::instance();
		$this->assertInstanceOf('SN_Lead_Manager_Lead', $manager);

		$leadData = [
			'home_owner' => '1',
			'property_value' => '100000',
			'mortgage_balance' => '50000',
			'debt_amt' => '$5,000 - $10,000',
		];

		$equity_utilization = $manager->calculate_equity_utilization($leadData);
		$this->assertEquals($equity_utilization, 50);
	}

	public function testEquityCalculation() {
		$manager = SN_Lead_Manager_Lead::instance();
		$this->assertInstanceOf('SN_Lead_Manager_Lead', $manager);

		$leadData = [
			'home_owner' => '1',
			'property_value' => '100000',
			'mortgage_balance' => '50000',
			'debt_amt' => '$5,000 - $10,000',
		];

		$equity = $manager->calculate_equity($leadData);
		$this->assertEquals($equity, 40000);
	}

	public function testRawEquityCalculation() {
		$manager = SN_Lead_Manager_Lead::instance();
		$this->assertInstanceOf('SN_Lead_Manager_Lead', $manager);

		$leadData = [
			'home_owner' => '1',
			'property_value' => '100000',
			'mortgage_balance' => '50000',
			'debt_amt' => '$5,000 - $10,000',
		];

		$equity = $manager->calculate_raw_equity($leadData);
		$this->assertEquals($equity, 50000);
	}

	public function testGetTrustPilotURL() {
		$manager = SN_Lead_Manager_Lead::instance();
		$this->assertInstanceOf('SN_Lead_Manager_Lead', $manager);

		$token = $manager->get_trust_pilot_url('hhfhs@gmail.com', 'pepe', '');
		// var_dump($token);
		$this->assertTrue( !empty($token) );
		$this->assertTrue( strpos( $token, urlencode('https://') ) === false );
	}

}