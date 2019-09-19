<div class="wrap snlm-stats">
	<h1>SN Lead Manager Stats</h1>
	<canvas height="50" class="stats-chart js-stats-chart" data-stats='<?php SN_Lead_Manager_Stats::getData() ?>'></canvas>
	<h3>Stats By Provider</h3>
	<table class="wp-list-table widefat striped">
		<thead>
			<tr>
				<th rowspan="2">Provider</th>
				<th rowspan="2">Slug</th>
				<th colspan="2">Today</th>
				<th colspan="2">This Month</th>
				<th colspan="2">Overall</th>
			</tr>
			<tr>
				<th>Emails</th>
				<th>Website Clicks</th>
				<th>Emails</th>
				<th>Website Clicks</th>
				<th>Emails</th>
				<th>Website Clicks</th>
			</tr>
		</thead>
		<?php $providers = SN_Lead_Manager_Stats::getProviders() ?>
		<tbody>
			<?php foreach($providers as $p): ?>
			<tr>
				<td><?php echo $p->post_title ?></td>
				<td><?php echo $p->meta_value ?></td>
				<td class="stat"><?php echo $p->stats->emails_today ?></td>
				<td class="stat"><?php echo $p->stats->website_today ?></td>
				<td class="stat"><?php echo $p->stats->emails_this_month ?></td>
				<td class="stat"><?php echo $p->stats->website_this_month ?></td>
				<td class="stat"><?php echo $p->stats->emails_overall ?></td>
				<td class="stat"><?php echo $p->stats->website_overall ?></td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>