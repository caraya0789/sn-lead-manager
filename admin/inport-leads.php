<div class="wrap">
	<h3>Inport Leads from LeadConduit</h3>
	<?php
	if(!empty($_POST) && $_FILES['leads']['size'] > 0): ?>
	<?php
		if ( ! function_exists( 'wp_handle_upload' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$uploadedfile = $_FILES['leads'];
		$upload_overrides = array( 'test_form' => false );

		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

		if ( $movefile && ! isset( $movefile['error'] ) ) {
		    $inportFiles = get_option('sn_inport_files', array());
			$inportFiles[] = $movefile;

			update_option('sn_inport_files', $inportFiles);
		} else {
		    echo '<p>'.$movefile['error'].'</p>';
		}
	?>
    <?php endif; ?>
	<form method="post" enctype="multipart/form-data">
		<p><input type="file" name="leads" /></p>
		<p><button name="action" value="inport_leads" class="button button-primary">Inport</button></p>
	</form>
	<?php $inportFiles = get_option('sn_inport_files', array()); ?>
	<?php if(count($inportFiles)): ?>
	<table class="table widefat">
		<thead>
			<tr>
				<td>File</td>
				<td>URL</td>
				<td>Type</td>
				<td>Action</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach($inportFiles as $k => $f): ?>
			<tr>
				<td><?php echo $f['file'] ?></td>
				<td><?php echo $f['url'] ?></td>
				<td><?php echo $f['type'] ?></td>
				<td><form method="post"><input type="hidden" name="action" value="use_file"><button name="use" value="<?php echo $k ?>" class="button button-primary">Use This</button></form></td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
	<?php endif ?>
	<p>&nbsp;</p>
	<?php if(!empty($_POST['action']) && $_POST['action'] == 'use_file' && isset($_POST['use'])): ?>
	<?php
		$columns = array();
		$good = array();
		if (($handle = fopen($inportFiles[$_POST['use']]['file'], "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	$num = count($data);
		    	if(count($columns) == 0) {
		    		for ($c=0; $c < $num; $c++)
			            $columns[$c] = $data[$c];
		    	} else {
		    		$result_row = array();
		    		for ($c=0; $c < $num; $c++) {
			            $result_row[$columns[$c]] = $data[$c];
		    		}
		    		if(!empty($result_row['delivered']))
			        	$good[] = $result_row;
		    	}
		    }
		    fclose($handle);
		}
	?>
	<?php $count = count($good); ?>
	<p>Found <strong><?php echo $count ?></strong> good leads</p>
	<?php 
		$perpage = 1000;
		$pages = (($count - ($count%$perpage)) / $perpage) + 1;
		$page = !empty($_POST['page']) ? $_POST['page'] : 0;
	?>
	<p><?php echo $pages ?> páginas a procesar.</p>
	<form method="post">
		<p><button name="sub_action" value="process" class="button button-primary">Start</button></p>
		<input type="hidden" name="action" value="use_file">
		<input type="hidden" name="use" value="<?php echo $_POST['use'] ?>" />
		<input type="hidden" name="page" value="<?php echo $page + 1 ?>" />
	</form>
	<?php if($_POST['sub_action'] == 'process'): ?>
	<p>Page: <?php echo $page ?></p>
	<?php
		global $wpdb;
		$offset = ($page - 1) * $perpage;
		$limit = $offset + $perpage;

		$new = 0;
		$updated = 0;
		$existing = 0;

		for($i=$offset; $i<$limit; $i++) {
			if(!isset($good[$i]))
				break;

			$row = $good[$i]; 
			//var_dump($row['captureDateAndTime']);
			$what = wp_insert_post(array(
				'post_author' => 1,
				'post_title' => strtolower($row['email']),
				'post_date' => date('Y-m-d H:i:s', strtotime($row['captureDateAndTime'])),
				'post_type' => 'leads',
				'post_status' => 'publish',
				'meta_input' => array(
					'lead_name' => $row['first_name'] . ' ' . $row['last_name'],
					'lead_delivered_to' => $row['delivered']
				)
			), true); ?>
			<pre>
			<?php var_dump($what) ?>
			</pre>
			<p>Inserted lead: <?php echo $row['email'] ?></p>
			<?php
		}
	?>
	<?php endif ?>
	<?php endif ?>
</div>