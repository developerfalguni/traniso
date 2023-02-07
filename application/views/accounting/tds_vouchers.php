<?php 
echo form_open($this->uri->uri_string(), 'id="MainForm"');

if (count($rows) == 0) :
	echo form_dropdown('month', $months) .
		'&nbsp;<button type="submit" class="btn btn-success">Show</button>
	</form>';
else :
?>
<input type="hidden" name="vouchers" value="1" />

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
	
	<!-- <div class="card-body"></div> -->

	<table class="table table-condensed table-striped table-bordered">
	<thead>
	<tr>
		<th>Voucher No</th>
		<th>Date</th>
		<th>Debit</th>
		<th>Amount</th>
		<th>TDS %</th>
		<th>TDS</th>
		<th>Surcharge</th>
		<th>Edu Cess</th>
		<th>HEdu Cess</th>
		<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
	</tr>
	</thead>

	<tbody>
	<?php 
		$total = 0;
		foreach ($rows as $r) {
			$total += ($r['tds_payment_id'] > 0 ? ($r['tds_amount'] + $r['tds_surcharge'] + $r['tds_edu_cess'] + $r['tds_hedu_cess']) : 0);
			echo '<tr>
		<td>' . $r['id2_format'] . '</td>
		<td>' . $r['date'] . '</td>
		<td>' . $r['debit_name'] . '</td>
		<td class="alignright">' . $r['invoice_amount'] . '</td>
		<td class="alignright">' . $r['tds'] . '</td>
		<td class="alignright">' . $r['tds_amount'] . '</td>
		<td class="alignright">' . $r['tds_surcharge'] . '</td>
		<td class="alignright">' . $r['tds_edu_cess'] . '</td>
		<td class="alignright">' . $r['tds_hedu_cess'] . '</td>
		<td class="aligncenter">' . form_checkbox(array(
			'name' => 'select_id['.$r['id'].']', 
			'value' => $r['id'], 
			'checked' => ($r['tds_payment_id'] > 0 ? true : false), 
			'class' => 'DeleteCheckbox'
		)) . '</td>
	</tr>';
		}
		?>
		</tbody>
		</table>
	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-md-4"><button type="submit" class="btn btn-success" id="Update">Update</button></div>
			<div class="col-md-8 alignright bold">Total: ' . inr_format($total) . '</div>
		</div>
	</div>
</div>

</form>
<?php endif; ?>