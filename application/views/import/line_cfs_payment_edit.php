<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
?>

<div class="row">
	<div class="col-md-6">
		<div class="card card-default">
			<div class="card-header">
				<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
				<div class="card-tools">
		  			<ol class="breadcrumb float-sm-right m-0">
		      			<li class="breadcrumb-item"><a href="#"><?php echo anchor('main','Dashboard') ?></a></li>
		      			<li class="breadcrumb-item"><?php echo humanize(clean($this->_clspath)) ?></li>
		      			<li class="breadcrumb-item active mr-1"><?php echo humanize($this->_class) ?> edit</li>
		    		</ol>
				</div>
			</div>
			
			<div class="card-body">
				<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th rowspan="2">Particular</th>
						<th rowspan="2">Calc Type</th>
						<th colspan="3" class="aligncenter">Container 20</th>
						<th colspan="3" class="aligncenter">Container 40</th>
					</tr>

					<tr>
						<th>Qty</th>
						<th>Rate</th>
						<th>Amount</th>
						<th>Qty</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>

				<tbody>
				<?php
					$particular_amt_20 = $particular_amt_40 = 0;
					$particular_amt_20_wo_total = $particular_amt_40_wo_total = 0;
					$particular_amt_20_with_tax_total = $particular_amt_40_with_tax_total = 0;
					$result = array(
						'amount_20' => 0,
						'amount_40' => 0,
						'amount'    => 0,
					);
					$container_20 = 0;
					$container_40 = 0;
					if (count($line_containers) == 0) {
						for($i = 0; $i < $job['container_20']; $i++) {
							$container_20++;
							$line_containers[] = array('size' => 20);
						}
						for($i = 0; $i < $job['container_40']; $i++) {
							$container_40++;
							$line_containers[] = array('size' => 40);
						}
					}
					else {
						foreach ($line_containers AS $c) {
							if ($c['size'] == 20)
								$container_20++;
							else
								$container_40++;
						}
					}

					$vessel = $this->kaabar->getRow('vessels', $job['vessel_id'], 'id');
					foreach ($line_rates as $r) {
						$tariffs = $this->kaabar->getRows('agent_tariffs', array('agent_rate_id' => $r['id']));
						$amount  = 0;
						$stax    = 0;
						$edu     = 0;
						$hedu    = 0;
						foreach($line_containers as $c) {
							if ($gld_date == '00-00-0000') {
								$days = daysDiff($vessel['eta_date'], $c['gatepass_date']);
							}
							else
								$days = daysDiff($gld_date, $c['gatepass_date']);
							$days -= $free_days;


							if ($tariffs AND (
								($gld_date != '0000-00-00' AND $c['gatepass_date'] != '0000-00-00') OR 
								($container_20 > 0 OR $container_40 > 0))) {
								$break = false;
								foreach ($tariffs as $t) {
									if ($t['tariff_type'] == 'Delivery Order') {
										if ($container_20 > 0 AND $t['from_day'] <= $container_20 AND $t['to_day'] >= $container_20) {
											$amount += $t['price_20'];
											$container_20 = 0;
										}
										if ($container_40 > 0 AND $t['from_day'] <= $container_40 AND $t['to_day'] >= $container_40) {
											$amount += $t['price_40'];
											$container_40 = 0;
										}
										continue;
									}
									
									if ($free_days > $t['from_day'] && $free_days > $t['to_day']) continue;

									if ($t['to_day'] == 0)
										$calc_day = $days;
									else {
										$calc_day = ($t['to_day'] - $t['from_day']) + 1;
										$days -= $calc_day;
										if ($days < 0) {
											$calc_day = $calc_day + $days;
											$break = true;
										}
									}
									if ($c['size'] == 20) {
										$amount += bcmul($calc_day, $t['price_20'], 0);
										$result['amount_20'] = bcadd($result['amount_20'], bcmul($calc_day, $t['price_20'], 0), 0);
									}
									else {
										$amount += bcmul($calc_day, $t['price_40'], 0);
										$result['amount_40'] = bcadd($result['amount_40'], bcmul($calc_day, $t['price_40'], 0), 0);
									}
									if ($break) break;
								}
							}
							else {
								if ($c['size'] == 20) {
									$amount += $r['price_20'];
									$result['amount_20'] = bcadd($result['amount_20'], $r['price_20'], 0);
								}
								else {
									$amount += $r['price_40'];
									$result['amount_40'] = bcadd($result['amount_40'], $r['price_20'], 0);
								}

								if ($r['calc_type'] == 'Fixed')
									break;
							}
						}

						if ($r['currency_id'] > 1) {
							$amount = bcmul($amount, $vessel['import_exchange_rate'], 0);
						}

						if ($r['taxable'] == true) {
							$stax = round($amount * Settings::get('service_tax') / 100, 2);
							$edu  = round($stax * Settings::get('edu_cess') / 100, 2);
							$hedu = round($stax * Settings::get('hedu_cess') / 100, 2);
						}
						$result['amount'] += ($amount + $stax + $edu + $hedu);
						
						$particular_amt_20 = (($r['calc_type'] == 'Fixed' && $container_20 > 0) ? '1' : $container_20) * $r['price_20'];
						$particular_amt_40 = (($r['calc_type'] == 'Fixed' && $container_40 > 0) ? '1' : $container_40) * $r['price_40'];
						$particular_amt_20_wo_total += $particular_amt_20;
						$particular_amt_40_wo_total += $particular_amt_40;
						$particular_amt_20_with_tax_total += ($particular_amt_20 > 0) ? ($particular_amt_20 + $stax + $edu + $hedu) : 0;
						$particular_amt_40_with_tax_total += ($particular_amt_40 > 0) ? ($particular_amt_40 + $stax + $edu + $hedu) : 0;

						echo '<tr>
							<td>'. $r['particulars'] .'</td>
							<td>'. $r['calc_type'] .'</td>
							<td class="aligncenter">'. (($r['calc_type'] == 'Fixed' && $container_20 > 0) ? '1' : $container_20) .'</td>
							<td class="alignright">'. inr_format($r['price_20']) .'</td>
							<td class="alignright">'. inr_format($particular_amt_20) .'</td>
							<td class="aligncenter">'. (($r['calc_type'] == 'Fixed' && $container_40 > 0) ? '1' : $container_40) .'</td>
							<td class="alignright">'. inr_format($r['price_40']) .'</td>
							<td class="alignright">'. inr_format($particular_amt_40) .'</td>
						</tr>';
					}

					echo '<tr>
						<th colspan="2" class="alignright">Total without Tax</th>
						<th class="aligncenter">' . $container_20 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_20_wo_total) . '</th>
						<th class="aligncenter">' . $container_40 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_40_wo_total) . '</th>
					</tr>

					<tr>
						<th colspan="2" class="alignright">Total</th>
						<th class="aligncenter">' . $container_20 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_20_with_tax_total) . '</th>
						<th class="aligncenter">' . $container_40 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_40_with_tax_total) . '</th>
					</tr>';
				?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
	
	<div class="col-md-6">
		<div class="card card-default">
			<div class="card-header">
				<h3 class="card-title">CFS Payment</h3>
			</div>
			
			<div class="card-body">
				<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th rowspan="2">Particular</th>
						<th rowspan="2">Calc Type</th>
						<th colspan="3" class="aligncenter">Container 20</th>
						<th colspan="3" class="aligncenter">Container 40</th>
					</tr>

					<tr>
						<th>Qty</th>
						<th>Rate</th>
						<th>Amount</th>
						<th>Qty</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>

				<tbody>
				<?php
					$particular_amt_20 = $particular_amt_40 = 0;
					$particular_amt_20_wo_total = $particular_amt_40_wo_total = 0;
					$particular_amt_20_with_tax_total = $particular_amt_40_with_tax_total = 0;
					$result = array(
						'amount_20' => 0,
						'amount_40' => 0,
						'amount'    => 0,
					);
					
					$container_20 = 0;
					$container_40 = 0;
					if (count($cfs_containers) == 0) {
						for($i = 0; $i < $job['container_20']; $i++) {
							$container_20++;
							$cfs_containers[] = array('size' => 20);
						}
						for($i = 0; $i < $job['container_40']; $i++) {
							$container_40++;
							$cfs_containers[] = array('size' => 40);
						}
					}
					else {
						foreach ($cfs_containers AS $c) {
							if ($c['size'] == 20)
								$container_20++;
							else
								$container_40++;
						}
					}

					$vessel = $this->kaabar->getRow('vessels', $job['vessel_id'], 'id');
					foreach ($cfs_rates as $r) {
						$tariffs = $this->kaabar->getRows('agent_tariffs', array('agent_rate_id' => $r['id']));
						$amount  = 0;
						$stax    = 0;
						$edu     = 0;
						$hedu    = 0;
						foreach($cfs_containers as $c) {
							if ($gld_date == '00-00-0000') {
								$days = daysDiff($vessel['eta_date'], $c['gatepass_date']);
							}
							else
								$days = daysDiff($gld_date, $c['gatepass_date']);
							$days -= $free_days;


							if ($tariffs AND (
								($gld_date != '0000-00-00' AND $c['gatepass_date'] != '0000-00-00') OR 
								($container_20 > 0 OR $container_40 > 0))) {
								$break = false;
								foreach ($tariffs as $t) {
									if ($t['tariff_type'] == 'Delivery Order') {
										if ($container_20 > 0 AND $t['from_day'] <= $container_20 AND $t['to_day'] >= $container_20) {
											$amount += $t['price_20'];
											$container_20 = 0;
										}
										if ($container_40 > 0 AND $t['from_day'] <= $container_40 AND $t['to_day'] >= $container_40) {
											$amount += $t['price_40'];
											$container_40 = 0;
										}
										continue;
									}
									
									if ($free_days > $t['from_day'] && $free_days > $t['to_day']) continue;

									if ($t['to_day'] == 0)
										$calc_day = $days;
									else {
										$calc_day = ($t['to_day'] - $t['from_day']) + 1;
										$days -= $calc_day;
										if ($days < 0) {
											$calc_day = $calc_day + $days;
											$break = true;
										}
									}
									if ($c['size'] == 20) {
										$amount += bcmul($calc_day, $t['price_20'], 0);
										$result['amount_20'] = bcadd($result['amount_20'], bcmul($calc_day, $t['price_20'], 0), 0);
									}
									else {
										$amount += bcmul($calc_day, $t['price_40'], 0);
										$result['amount_40'] = bcadd($result['amount_40'], bcmul($calc_day, $t['price_40'], 0), 0);
									}
									if ($break) break;
								}
							}
							else {
								if ($c['size'] == 20) {
									$amount += $r['price_20'];
									$result['amount_20'] = bcadd($result['amount_20'], $r['price_20'], 0);
								}
								else {
									$amount += $r['price_40'];
									$result['amount_40'] = bcadd($result['amount_40'], $r['price_20'], 0);
								}

								if ($r['calc_type'] == 'Fixed')
									break;
							}
						}

						if ($r['currency_id'] > 1) {
							$amount = bcmul($amount, $vessel['import_exchange_rate'], 0);
						}

						if ($r['taxable'] == true) {
							$stax = round($amount * Settings::get('service_tax') / 100, 2);
							$edu  = round($stax * Settings::get('edu_cess') / 100, 2);
							$hedu = round($stax * Settings::get('hedu_cess') / 100, 2);
						}
						$result['amount'] += ($amount + $stax + $edu + $hedu);
						
						$particular_amt_20 = (($r['calc_type'] == 'Fixed' && $container_20 > 0) ? '1' : $container_20) * $r['price_20'];
						$particular_amt_40 = (($r['calc_type'] == 'Fixed' && $container_40 > 0) ? '1' : $container_40) * $r['price_40'];
						$particular_amt_20_wo_total += $particular_amt_20;
						$particular_amt_40_wo_total += $particular_amt_40;
						$particular_amt_20_with_tax_total += ($particular_amt_20 > 0) ? ($particular_amt_20 + $stax + $edu + $hedu) : 0;
						$particular_amt_40_with_tax_total += ($particular_amt_40 > 0) ? ($particular_amt_40 + $stax + $edu + $hedu) : 0;

						echo '<tr>
							<td>'. $r['particulars'] .'</td>
							<td>'. $r['calc_type'] .'</td>
							<td class="aligncenter">'. (($r['calc_type'] == 'Fixed' && $container_20 > 0) ? '1' : $container_20) .'</td>
							<td class="alignright">'. inr_format($r['price_20']) .'</td>
							<td class="alignright">'. inr_format($particular_amt_20) .'</td>
							<td class="aligncenter">'. (($r['calc_type'] == 'Fixed' && $container_40 > 0) ? '1' : $container_40) .'</td>
							<td class="alignright">'. inr_format($r['price_40']) .'</td>
							<td class="alignright">'. inr_format($particular_amt_40) .'</td>
						</tr>';
					}
					
					echo '<tr>
						<th colspan="2" class="alignright">Total without Tax</th>
						<th class="aligncenter">' . $container_20 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_20_wo_total) . '</th>
						<th class="aligncenter">' . $container_40 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_40_wo_total) . '</th>
					</tr>

					<tr>
						<th colspan="2" class="alignright">Total</th>
						<th class="aligncenter">' . $container_20 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_20_with_tax_total) . '</th>
						<th class="aligncenter">' . $container_40 . '</th>
						<th></th>
						<th class="alignright">' . inr_format($particular_amt_40_with_tax_total) . '</th>
					</tr>';
				?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>