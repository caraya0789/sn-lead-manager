<style>
.snlm-test-routing fieldset {
	overflow: hidden;
}
.snlm-test-routing fieldset label {
	color: #222;
    float: left;
    font-weight: 600;
    line-height: 1.3;
    padding: 20px 10px 20px 0;
    vertical-align: top;
    width: 200px;
}
.snlm-test-routing fieldset .form-field {
	float: left;
	line-height: 1.3;
    max-width: 100%;
    padding: 15px 10px;
    vertical-align: middle;
}
.snlm-test-routing fieldset .form-field .form-control {
	width: 25em;
}
</style>
<div class="wrap snlm-test-routing">
	<h2>Test Routing</h2>
	<form action="" method="post">
		<fieldset>
			<label>Debt Amount</label>
			<div class="form-field">
				<select name="debt_amt" class="form-control">
					<option <?php echo $_POST['debt_amt'] == '$0 - $4,999' ? 'selected' : '' ?>>$0 - $4,999</option>
					<option <?php echo $_POST['debt_amt'] == '$5,000 - $9,999' ? 'selected' : '' ?>>$5,000 - $9,999</option>
					<option <?php echo $_POST['debt_amt'] == '$10,000 - $14,999' ? 'selected' : '' ?>>$10,000 - $14,999</option>
					<option <?php echo $_POST['debt_amt'] == '$15,000 - $19,999' ? 'selected' : '' ?>>$15,000 - $19,999</option>
					<option <?php echo $_POST['debt_amt'] == '$20,000 - $29,999' ? 'selected' : '' ?>>$20,000 - $29,999</option>
					<option <?php echo $_POST['debt_amt'] == '$30,000 - $39,999' ? 'selected' : '' ?>>$30,000 - $39,999</option>
					<option <?php echo $_POST['debt_amt'] == '$40,000 - $49,999' ? 'selected' : '' ?>>$40,000 - $49,999</option>
					<option <?php echo $_POST['debt_amt'] == '$50,000 - $59,999' ? 'selected' : '' ?>>$50,000 - $59,999</option>
					<option <?php echo $_POST['debt_amt'] == '$60,000 - $69,999' ? 'selected' : '' ?>>$60,000 - $69,999</option>
					<option <?php echo $_POST['debt_amt'] == '$70,000 - $79,999' ? 'selected' : '' ?>>$70,000 - $79,999</option>
					<option <?php echo $_POST['debt_amt'] == '$80,000 - $89,999' ? 'selected' : '' ?>>$80,000 - $89,999</option>
					<option <?php echo $_POST['debt_amt'] == '$90,000 - $99,999' ? 'selected' : '' ?>>$90,000 - $99,999</option>
					<option <?php echo $_POST['debt_amt'] == '$100,000' ? 'selected' : '' ?> value="$100,000">More Than $100,000</option>
				</select>
			</div>
		</fieldset>
		<fieldset>
			<label>State</label>
			<div class="form-field">
				<select name="state" class="form-control">
					<option <?php echo $_POST['state'] == 'AB' ? 'selected' : '' ?> value="AB">(AB) Alberta</option>
					<option <?php echo $_POST['state'] == 'BC' ? 'selected' : '' ?> value="BC">(BC) British Columbia</option>
					<option <?php echo $_POST['state'] == 'MB' ? 'selected' : '' ?> value="MB">(MB) Manitoba</option>
					<option <?php echo $_POST['state'] == 'NB' ? 'selected' : '' ?> value="NB">(NB) New Brunswick</option>
					<option <?php echo $_POST['state'] == 'NL' ? 'selected' : '' ?> value="NL">(NL) Newfoundland and Labrador, Newfoundland, Labrador</option>
					<option <?php echo $_POST['state'] == 'NT' ? 'selected' : '' ?> value="NT">(NT) Northwest Territories</option>
					<option <?php echo $_POST['state'] == 'NS' ? 'selected' : '' ?> value="NS">(NS) Nova Scotia</option>
					<option <?php echo $_POST['state'] == 'NU' ? 'selected' : '' ?> value="NU">(NU) Nunavut</option>
					<option <?php echo $_POST['state'] == 'ON' ? 'selected' : '' ?> value="ON">(ON) Ontario</option>
					<option <?php echo $_POST['state'] == 'PE' ? 'selected' : '' ?> value="PE">(PE) Prince Edward Island</option>
					<option <?php echo $_POST['state'] == 'QC' ? 'selected' : '' ?> value="QC">(QC) Quebec</option>
					<option <?php echo $_POST['state'] == 'SK' ? 'selected' : '' ?> value="SK">(SK) Saskatchewan</option>
					<option <?php echo $_POST['state'] == 'YT' ? 'selected' : '' ?> value="YT">(YT) Yuko</option>
				</select>
			</div>
		</fieldset>
		<fieldset>
			<label>Postal Code</label>
			<div class="form-field">
				<input name="postal_code" value="<?php echo !empty($_POST['postal_code']) ? $_POST['postal_code'] : '' ?>" type="text" class="form-control regular-text">
			</div>
		</fieldset>
		<input type="submit" name="submit-cmb" value="Test" class="button-primary">
	</form>
	<?php if($_POST): ?>
	<pre>
		<?php
			$manager = SN_Lead_Manager_Lead::instance();
			$providers = $manager->getElegibleProviders($_POST);
			// var_dump($providers);
			var_dump($manager->sortProviders($providers));
		?>
	</pre>
	<?php endif ?>
</div>