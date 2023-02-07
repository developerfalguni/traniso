

<?php
echo form_open($this->uri->uri_string(), 'class="form-horizontal" id="MainForm"');
$tab_general = '';
$tab_company = '';
$tab_smtp = '';
$tab_imap = '';
$tab_visualimpex = '';

foreach ($settings as $row) {
	if (substr($row['name'], 0, 7) == 'company')
		$tab_company .= '<div class="form-group">
			<label class="control-label col-md-2">' . humanize(str_replace('company_', '', $row['name'])) . '</label>
			<div class="col-md-10">
				<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control form-control-sm" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />
			</div>
		</div>';
	else if (substr($row['name'], 0, 4) == 'smtp')
		$tab_smtp .= '<div class="form-group">
			<label class="control-label col-md-2">' . humanize(str_replace('smtp_', '', $row['name'])) . '</label>
			<div class="col-md-10">
				<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control form-control-sm" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />
			</div>
		</div>';
	else if (substr($row['name'], 0, 4) == 'imap')
		$tab_imap .= '<div class="form-group">
			<label class="control-label col-md-2">' . humanize(str_replace('imap_', '', $row['name'])) . '</label>
			<div class="col-md-10">
				<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control form-control-sm" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />
			</div>
		</div>';
	else if (substr($row['name'], 0, 3) == 'vch'){

		$vch_name = str_replace('vch_', '', $row['name']);

		if($vch_name == 'inv_default_cr_ledger')
			$name = 'Invoice Default Credit Ledger';
		elseif($vch_name == 'dn_default_cr_ledger')
			$name = 'Debit Note Default Credit Ledger';
		elseif($vch_name == 'cn_default_dr_ledger')
			$name = 'Credit Note Default Debit Ledger';
		
		$tab_vch .= '<div class="form-group">
			<label class="control-label">' . $name . '</label>
			<input type="text" class="form-control form-control-sm Ledger" name="value[' . $row['id'] . ']" value="' . $row['value'] . '">
		</div>';
	}
	else if (substr($row['name'], 0, 4) == 'eway')

		if($row['name'] == 'eway_test_active' OR $row['name'] == 'eway_active'){
			$tab_ewaybill .= '<div class="mb-3">
				<label class="control-label">' . humanize($row['name']) . '</label>
				'.form_dropdown('value['.$row['id'] .']', $apiStatus, $row['value'], 'class="form-control"').'</div>';
		}
		elseif($row['name'] == 'eway_default_source'){
			$tab_ewaybill .= '<div class="mb-3">
				<label class="control-label">' . humanize($row['name']) . '</label>
				'.form_dropdown('value['.$row['id'] .']', $defaultSource, $row['value'], 'class="form-control"').'</div>';
		}
		else{
			$tab_ewaybill .= '<div class="mb-3">
				<label class="control-label">' . humanize($row['name']) . '</label>
				<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />
			</div>';
		}
	else if (substr($row['name'], 0, 4) == 'einv'){

		$name = humanize(str_replace('einv_', '', $row['name']));
		$name = 'eInvoice '.$name;
		
		if($row['name'] == 'einv_test_active' OR $row['name'] == 'einv_active'){
			$tab_einv .= '<div class="mb-3">
				<label class="control-label">' . $name . '</label>
				'.form_dropdown('value['.$row['id'] .']', $apiStatus, $row['value'], 'class="form-control"').'</div>';
		}
		elseif($row['name'] == 'einv_default_source'){
			$tab_einv .= '<div class="mb-3">
				<label class="control-label">' . $name . '</label>
				'.form_dropdown('value['.$row['id'] .']', $defaultSource, $row['value'], 'class="form-control"').'</div>';
		}
		else{
			$tab_einv .= '<div class="mb-3">
				<label class="control-label">' . $name . '</label>
				<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />
			</div>';
		}
	}
	else if (substr($row['name'], 0, 3) == 'sms')

		if($row['name'] == 'sms_api_active'){
			$tab_sms .= '<div class="mb-3">
				<label class="control-label">' . humanize($row['name']) . '</label>
				'.form_dropdown('value['.$row['id'] .']', $apiStatus, $row['value'], 'class="form-control"').'</div>';
		}
		else{
			$tab_sms .= '<div class="mb-3">
				<label class="control-label">' . humanize(str_replace('smtp_', '', $row['name'])) . '</label>
				<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />
			</div>';
		}
	else
	{

		$tab_general .= '<div class="form-group">
			<label class="control-label col-md-2">' . humanize($row['name']) . '</label>
			<div class="col-md-10">';
			
			if($row['name'] == 'default_company'){
				$tab_general .= form_dropdown('value['.$row['id'] .']', getSelectOptions('companies'), $row['value'], 'class="form-control"');
			}
			else
			{
				$tab_general .= '<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control form-control-sm" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />';
			}
			
			$tab_general .= '</div>
		</div>';
		
	}
}
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><?php echo $page_title ?></h3>
	</div>
	<div class="card-body">
		<ul class="nav nav-tabs">
			<li class="nav-item active"><a href="#tab1" class="nav-link active" data-toggle="tab">General</a></li>
			<li class="nav-item"><a href="#tab2" class="nav-link" data-toggle="tab">Company</a></li>
			<li class="nav-item"><a href="#tab3" class="nav-link" data-toggle="tab">SMTP</a></li>
			<li class="nav-item"><a href="#tab4" class="nav-link" data-toggle="tab">IMAP</a></li>
			<li class="nav-item"><a href="#tab5" class="nav-link" data-toggle="tab">SMS</a></li>
			<li class="nav-item"><a href="#tab6" class="nav-link" data-toggle="tab">Ewaybill</a></li>
			<li class="nav-item"><a href="#tab7" class="nav-link" data-toggle="tab">eInvoice</a></li>
			<!-- <li class="nav-item"><a href="#tab8" class="nav-link" data-toggle="tab">Voucher</a></li> -->			
		</ul>

		<div class="tab-content  mt-3">
			<div class="tab-pane fade show active" id="tab1">
				<?php echo $tab_general ?>
			</div>
			
			<div class="tab-pane fade" id="tab2">
				<?php echo $tab_company ?>
			</div>

			<div class="tab-pane fade" id="tab3">
				<?php echo $tab_smtp ?>
			</div>

			<div class="tab-pane fade" id="tab4">
				<?php echo $tab_imap ?>
			</div>
			<div class="tab-pane fade" id="tab5">
				<?php echo $tab_sms ?>
			</div>
			<div class="tab-pane fade" id="tab6">
				<?php echo $tab_ewaybill ?>
			</div>
			<div class="tab-pane fade" id="tab7">
				<?php echo $tab_einv ?>
			</div>
			<!-- <div class="tab-pane fade" id="tab8">
				<?php //echo $tab_vch ?>
			</div> -->
		</div>
		
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>
