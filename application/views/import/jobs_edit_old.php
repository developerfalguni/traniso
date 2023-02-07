<?php
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm" enctype="multipart/form-data"');
echo form_hidden(array('id' => 0));

?>

<style type="text/css">
	.modal-body {
	    /* 100% = dialog height, 120px = header + footer */
	    max-height: calc(50%);
	    overflow-y: scroll;
	}
</style>


<div class="card-header">
	<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
	<div class="card-tools">
			<ol class="breadcrumb mb-0">
			<div class="form-group mb-0">
				<?php echo form_dropdown('job_list', '', '', 'class="form-control mb-0 width-300" id="jobSelect2"'); ?>
			</div>
  		</ol>
	</div>
</div>
<div class="card card-default">
	<div class="card-body">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group row">
					<label class="col-sm-3 col-form-label col-form-label-sm">Select Branch : </label>
					<div class="col-sm-9">
						<?php echo form_dropdown('branch_id', getSelectOptions('branches', 'id', 'name', 'where company_id = '.$company_id), '', 'class="form-control form-control-sm select2" id="branch_id"') ?>
					</div>
				</div>
			</div>
		</div>	
		<div class="row" id="subType"> 
			<?php foreach ($sub_type as $key => $value) { ?>
				<div class="col-md-4">
					<div class="form-group">
						<input type="checkbox" id="<?php echo $value ?>" name="sub_type[<?php echo strtolower($value) ?>]" value="<?php echo $value ?>" >
						<label><?php echo $value ?></label>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Job No</label>
					<input type="text" name="idkaabar_code" id="idkaabar_code" class="form-control form-control-sm" value="" readonly>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control DatePicker" name="date" value="<?php echo date('d-m-Y') ?>" maxlength="10" id="date" />
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Shipment Type</label>
					<?php echo form_dropdown('shipment_type', $this->export->getContainerShipmentTypes(), '', 'class="form-control form-control-sm" id="shipment_type"') ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Job Reference No</label>
					<input type="text" class="form-control form-control-sm" name="job_reference" id="job_reference" value="">
				</div>
			</div>
		</div>
	</div>
	<div class="card-header">
		<h3 class="card-title">Billing Party 1</h3>
	</div>
	<div class="card-body" id="firstParty">
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label text-red">Billing Party Name 1</label>
					<input type="text" class="form-control form-control-sm" name="party_name" id="party_name">
					<input type="hidden" name="party_id" title="id" value="" id="party_id">
					<input type="hidden" name="party_category" title="category" value="" id="party_category">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Shipper Name (Exporter Name)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="shipper_name" value="" id="shipper_name" />
						<input type="hidden" name="shipper_id" title="id" value="" id="shipper_id">
						<input type="hidden" name="shipper_category" title="category" value="" id="shipper_category">


						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updateMaster('agent', 'Exporter')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Consignee Name</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="consignee_name" value="" id="consignee_name">
						<input type="hidden" name="consignee_id" title="id" value="" id="consignee_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updateMaster('agent', 'Consignee')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Buyer Name</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm" name="buyer_name" value="" id="buyer_name" />
						<input type="hidden" name="buyer_id" title="id" value="" id="buyer_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updateMaster('agent', 'Buyer')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Notify</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm" name="notify_name" value="" size="30" id="notify_name">
						<input type="hidden" name="notify_id" title="id" value="" id="notify_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updateMaster('agent', 'Notify')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Clearance Port</label>
					<input type="text" class="form-control form-control-sm" name="clearance_port" value="" id="clearance_port">
					<input type="hidden" name="clearance_port_id" title="id" value="" id="clearance_port_id">
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Invoice No</label>
							<input type="text" class="form-control form-control-sm" name="invoice_no" value="" id="invoice_no">
						</div>
					</div>
					<div class="col-md-6 pl-0">
						<div class="form-group">
							<label class="control-label">Invoice Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="invoice_date" value="<?php echo date('d-m-Y') ?>" id="invoice_date">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Invoice Type</label>
					<?php echo form_dropdown('invoice_types', $this->export->getInvoiceTypes(), '', 'class="form-control form-control-sm" id="invoice_types"'); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">POR (Port of Receipt)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="por_name" value="" id="por_name">
						<input type="hidden" name="por_id" title="id" value="" id="por_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updatePort('port', 'Global')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">POL (Port of Loading)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="pol_name" value="" id="pol_name">
						<input type="hidden" name="pol_id" title="id" value="" id="pol_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updatePort('port', 'Global')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">POD (Dischrge Port)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="pod_name" value="" id="pod_name">
						<input type="hidden" name="pod_id" title="id" value="" id="pod_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updatePort('port', 'Global')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">FPOD (Destination Port)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="fpod_name" value="" id="fpod_name">
						<input type="hidden" name="fpod_id" title="id" value="" id="fpod_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updatePort('port', 'Global')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">CHA</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="cha_name" value="" id="cha_name">
						<input type="hidden" name="cha_id" title="id" value="" id="cha_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updateMaster('agent', 'CHA')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Shipping Line</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="line_name" value="" id="line_name" />
						<input type="hidden" name="line_id" title="id" value="" id="line_id" />
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updateMaster('agent', 'Line')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Forwarder</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="forwarder_name" value="" id="forwarder_name" />
						<input type="hidden" name="forwarder_id" title="id" value="" id="forwarder_id">
						<span class="input-group-append">
							<a href="#" class="btn btn-info" onclick="updateMaster('agent', 'Forwarder')"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Stuffing Type</label>
					<?php echo form_dropdown('delivery_type', $this->export->getStuffingTypes(), '', 'class="form-control form-control-sm" id="delivery_type"') ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">No. Packages</label>
					<input type="text" class="form-control form-control-sm Numeric" name="packages" onkeypress="return isNumber(event)" value="" id="packages">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Packages Type</label>
					<?php echo form_dropdown('package_type', getSelectOptions('package_types', 'id', 'name'), '', 'class="form-control form-control-sm" id="package_type"'); ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Gross Weight</label>
							<input type="text" class="form-control form-control-sm Numeric" onkeypress="return isNumberDot(event)" name="gross_weight" value="" size="12" id="gross_weight" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Net Weight</label>
							<input type="text" class="form-control form-control-sm Numeric" onkeypress="return isNumberDot(event)" name="net_weight" value="" size="12" id="net_weight" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Unit</label>
					<?php echo form_dropdown('unit_id', getSelectOptions('units', 'id', 'name'), '', 'class="form-control form-control-sm" id="unit_id"'); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Shipment Terms</label>
					<?php echo form_dropdown('incoterms', $this->export->getIncoTerms(), '', 'class="form-control form-control-sm" id="incoterms"') ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">SB No</label>
							<input type="text" class="form-control form-control-sm" name="sb_no" value="" id="sb_no" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">SB Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="sb_date" value="<?php echo date('d-m-Y') ?>" id="sb_date">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Item Description</label>
					<textarea rows="1" class="form-control form-control-sm" name="item_description" id="item_description"></textarea>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Remarks</label>
					<textarea rows="1" class="form-control form-control-sm" name="remarks" id="remarks"></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="card-header">
		<h3 class="card-title">Billing Party 2</h3>
	</div>
	<div class="card-body" id="secondParty">
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label text-red">Billing Party 2</label>
					<input type="text" class="form-control form-control-sm" name="party_name1" value="" size="30" id="party_name1">
					<input type="hidden" name="party_id1" title="id" id="party_id1" value="">
					<input type="hidden" name="party_category1" title="category" id="party_category1" value="">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Booking No</label>
					<input type="text" class="form-control form-control-sm" name="booking_no" value="" id="booking_no" />
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Booking Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control DatePicker" name="booking_date" value="<?php echo date('d-m-Y') ?>" id="booking_date">
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Booking Validity</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control DatePicker" name="booking_validity" value="<?php echo date('d-m-Y') ?>" id="booking_validity">
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">MBL No</label>
							<input type="text" class="form-control form-control-sm" name="mbl_no" value="" id="mbl_no" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">MBL Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="mbl_date" value="<?php echo date('d-m-Y') ?>" id="mbl_date" />
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">MBL Type</label>
							<input type="text" class="form-control form-control-sm" name="mbl_type" value="" id="mbl_type" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">HBL No</label>
							<input type="text" class="form-control form-control-sm" name="hbl_no" value="" id="hbl_no" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">HBL Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="hbl_date" value="<?php echo date('d-m-Y') ?>" id="hbl_date" />
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">HBL Type</label>
							<input type="text" class="form-control form-control-sm" name="hbl_type" value="" id="hbl_type" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="card-header pt-0">
		<div class="row d-none" id="jobInfoButtons">
			<div class="col-md-10">
				<button type="button" data-toggle="modal" class="btn btn-info ml-2" onclick="javascript: updateContainers()"> <i class="fa fa-truck"></i> Update Containers</button>
				<button type="button" class="btn btn-info ml-2" onclick="javascript: updateCostsheet()"> <i class="fa fa-rupee-sign"></i> Update Costsheet</button>
				<button type="button" data-toggle="modal" class="btn btn-info ml-2" onclick="javascript: attachDocuments()"> <i class="fa fa-upload"></i> Upload Documents</button>
			</div>
			<div class="col-md-1"></div>
		</div>
	</div>
	
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-success" id="Add" onclick="updateJob('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Delete</button>
				</div>
			</div>
		</div>
	</div>
</div>

</form>
<?php $this->load->view($this->_clspath.'models') ?>

<script id="AC_PortCountry" type="text/x-handlebars-template">
	<li><a>{{name}} <span class="tiny"><span class="orange">{{unece_code}}</span> {{country}}</span></a></li>
</script>
<script id="AC_ContaineSize" type="text/x-handlebars-template">
	<li><a>{{size}} <span class="tiny"><span class="orange">{{code}}</span> {{fullname}}</span></a></li>
</script>

<script type="text/javascript">

	$(document).ready(function() {
		
		updateJob('0');
		
		var $shipment_type = $('#shipment_type').selectize();
		var $invoice_types = $('#invoice_types').selectize();
		var $delivery_type = $('#delivery_type').selectize();
		var $package_type = $('#package_type').selectize();
		var $unit_id = $('#unit_id').selectize();
		var $incoterms = $('#incoterms').selectize();

		$('#mbl_type').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/jobs/id/mbl_type') ?>'});

		$('#hbl_type').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/jobs/id/hbl_type') ?>'}); 
		
		$('#party_name').kaabar_autocomplete({source: '<?php echo site_url('master/operation/party/ajax1') ?>', alertText: 'Billing Party 1'});
		$('#party_name1').kaabar_autocomplete({source: '<?php echo site_url('master/operation/party/ajax2') ?>', alertText: 'Billing Party 2'});

		$('#shipper_name').kaabar_autocomplete({source: '<?php echo site_url('master/operation/party/ajax1') ?>', alertText: 'Shipper name'});
		$('#consignee_name').kaabar_autocomplete({source: '<?php echo site_url('master/operation/consignee/ajax/Consignee') ?>', alertText: 'Consignee name'});
		$('#buyer_name').kaabar_autocomplete({source: '<?php echo site_url('master/operation/consignee/ajax/Consignee') ?>', alertText: 'Buyer name'});
		$('#notify_name').kaabar_autocomplete({source: '<?php echo site_url('master/operation/consignee/ajax/Consignee') ?>', alertText: 'Notify name'});
	
		$('#forwarder_name').kaabar_autocomplete({source: '<?php echo site_url('/master/operation/vendor/ajax') ?>', alertText: 'Forwarder name'});
		$('#line_name').kaabar_autocomplete({source: '<?php echo site_url('/master/operation/vendor/ajax') ?>', alertText: 'Shipping Line'});
		$('#cha_name').kaabar_autocomplete({source: '<?php echo site_url('/master/operation/vendor/ajax') ?>', alertText: 'CHA name'});

		
		$('#clearance_port').kaabar_autocomplete({source: '<?php echo site_url('master/port/ajax') ?>', handlebarID: '#AC_PortCountry'});
		$('#por_name').kaabar_autocomplete({source: '<?php echo site_url('master/port/ajax') ?>', handlebarID: '#AC_PortCountry'});
		$('#por_name').kaabar_autocomplete({source: '<?php echo site_url('master/port/ajax') ?>', handlebarID: '#AC_PortCountry'});
		$('#pol_name').kaabar_autocomplete({source: '<?php echo site_url('master/port/ajax') ?>', handlebarID: '#AC_PortCountry'});
		$('#pod_name').kaabar_autocomplete({source: '<?php echo site_url('master/port/ajax') ?>', handlebarID: '#AC_PortCountry'});
		$('#fpod_name').kaabar_autocomplete({source: '<?php echo site_url('master/port/ajax') ?>', handlebarID: '#AC_PortCountry'});

		$('#item_description').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/jobs/id/item_description') ?>'});

		$('#remarks').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/jobs/id/remarks') ?>'});
		

		$('#jobSelect2').select2({ 
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/jobsList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				beforeSend: function(){
		            $("#loader").show();
		        },
				processResults: function (response) {
					$("#loader").hide();
			  		return {
			     		results: response
			  		};
				},
				cache: true
			},
			
	    }).on("change", function () {
	    	//console.log('<?php echo site_url($this->_clspath.$this->_class) ?>/jobsList/');
	    	//console.log($(this).val())
	    	var job_id = $(this).val();
	    	$('#MainForm')[0].reset();
	    	$("input[name=id]").attr('value', '0');
	    	// $('#updateParty').html();
	    	updateJob(job_id);

	    });

	    $('.select2-selection__rendered').hover(function () {
		    $(this).removeAttr('title');
		});
		$(document).on('select2:open', () => {
	    	document.querySelector('.select2-search__field').focus();
	    });

	});


	
	function updateJob(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getJob/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {
                	// console.log(response);
                	if(response.success == false){

                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
   						$("#loader").hide();
					}
                	else
                	{

                		$('#MainForm')[0].reset();
	                	
                		$("input[name=id]").val(response.job.id);

						$('#branch_id').val(response.job.branch_id);
						$('#branch_id').trigger('change');

						//// For Shipement sub type
						$.each(response.job.sub_type, function(index, value) {
							$('#Clearing').prop('checked', false);
							$('#Forwarding').prop('checked', false);
							$('#Transportation').prop('checked', false);

					    	if(value === 'Clearing')
					    		$('#Clearing').prop('checked', true);
					    	if(value === 'Forwarding')
					    		$('#Forwarding').prop('checked', true);
					    	if(value === 'Transportation')
					    		$('#Transportation').prop('checked', true);
					    });
						

						$("#idkaabar_code").val(response.job.idkaabar_code);
						$("#date").val(response.job.date);
						$("#shipment_type").data('selectize').setValue(response.job.shipment_type);
						$("#job_reference").val(response.job.job_reference);

						$("#party_name").val(response.job.party_name);
						$("#party_id").val(response.job.billing_party_id);
						$("#party_category").val(response.job.billing_party_category);

						$("#shipper_name").val(response.job.shipper_name);
						$("#shipper_id").val(response.job.shipper_id);
						$("#shipper_category").val(response.job.shipper_category);

						$("#consignee_name").val(response.job.consignee_name);
						$("#consignee_id").val(response.job.consignee_id);

						$("#buyer_name").val(response.job.buyer_name);
						$("#buyer_id").val(response.job.buyer_id);

						$("#notify_name").val(response.job.notify_name);
						$("#notify_id").val(response.job.notify_id);

						$("#clearance_port").val(response.job.clearance_port);
						$("#clearance_port_id").val(response.job.clearance_port_id);

						$("#invoice_no").val(response.job.invoice_no);
						$("#invoice_date").val(response.job.invoice_date);
						$("#invoice_types").data('selectize').setValue(response.job.invoice_types);
						
						$("#por_name").val(response.job.por_name);
						$("#por_id").val(response.job.por_id);
						$("#pol_name").val(response.job.pol_name);
						$("#pol_id").val(response.job.pol_id);
						$("#pod_name").val(response.job.pod_name);
						$("#pod_id").val(response.job.pod_id);
						$("#fpod_name").val(response.job.fpod_name);
						$("#fpod_id").val(response.job.fpod_id);


						$("#cha_name").val(response.job.cha_name);
						$("#cha_id").val(response.job.cha_id);
	            		$("#line_name").val(response.job.line_name);
	            		$("#line_id").val(response.job.line_id);
	            		$("#forwarder_name").val(response.job.forwarder_name);
	            		$("#forwarder_id").val(response.job.forwarder_id);

	            		$("#delivery_type").data('selectize').setValue(response.job.delivery_type);

	            		$("#packages").val(response.job.packages);
	            		$("#package_type").data('selectize').setValue(response.job.package_type);
	            		
	            		$("#gross_weight").val(response.job.gross_weight);
	            		$("#net_weight").val(response.job.net_weight);
	            		$("#unit_id").data('selectize').setValue(response.job.unit_id);
	            		
	            		$("#incoterms").data('selectize').setValue(response.job.incoterms);
	            		
	            		$("#sb_no").val(response.job.sb_no);
	            		$("#sb_date").val(response.job.sb_date);

	            		$("#item_description").val(response.job.item_description);
	            		$("#remarks").val(response.job.remarks);

	            		$("#party_name1").val(response.job.party_name2);
	            		$("#party_id1").val(response.job.billing_party1_id);
	            		$("#party_category1").val(response.job.billing_party1_category);

	            		$("#booking_no").val(response.job.booking_no);
	            		$("#booking_date").val(response.job.booking_date);
	            		$("#booking_validity").val(response.job.booking_validity);

	            		$("#mbl_no").val(response.job.mbl_no);
	            		$("#mbl_date").val(response.job.mbl_date);
	            		$("#mbl_type").val(response.job.mbl_type);

	            		$("#hbl_no").val(response.job.hbl_no);
	            		$("#hbl_date").val(response.job.hbl_date);
	            		$("#hbl_type").val(response.job.hbl_type);

	            		$("#jobInfoButtons").removeClass('d-none');
	            		
	            		$("#loader").hide();
					}

				    $('#MainForm').unbind('submit').bind('submit', function(e) {

			        	e.preventDefault();
    					
                    	var formData = new FormData(this);
                    	// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: $(this).attr('action'),
						  	type: $(this).attr('method'),
						  	data: formData, // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	contentType: false,
    						processData: false,
    						beforeSend: function(){
    							$("#loader").show();
    						},
						  	success:function(response) {

						  		if(response.success === true) {
						  			$("#loader").hide();
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    });

						  			updateJob(id);
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								  	var arr = [];	
								    $.each(response.messages, function(index, value) {
								    	if(value.length){
								    		arr.push(value);
								    	}
								    });

								    /// Alert If Validation Failed
								    new PNotify({
						      			text: arr.join(", \n"),
										type: 'error',
										nonblock: {
											nonblock: true,
											nonblock_opacity: .2
										}
									});	
								    $("#loader").hide();
								  } 
								  else 
								  {
								  	$("#loader").hide();
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
								return false;
							}
						}); 
						return false;
					});

				}
            });
        }
    }

</script>

<script type="text/javascript">
	$('#modal-import-document').on('hidden.bs.modal', function () {
        $(this).find('#documentUpload').html('');
    });
    
    $('#modal-containers').on('hidden.bs.modal', function () {
        $(this).find('#updateContainers').html('');
    })

    $('#modal-container').on('hidden.bs.modal', function () {
        $(this).find('#updateContainer').html('');
    })

    $('#modal-costsheet').on('hidden.bs.modal', function () {
        $(this).find('#updateCostsheet').html('');
    })

    $('#modal-master-update').on('hidden.bs.modal', function () {
        $(this).find('#masterUpdate').html('');
    })

    $('#modal-port-update').on('hidden.bs.modal', function () {
        $(this).find('#portUpdate').html('');
    })

    function updateContainers(id = null)
    {   

    	var id = $("input[name=id]").val();

    	if(id > 0){
        	$.ajax({
                url: '<?php echo site_url() ?>import/container/index/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.rows.length){
				    	var result = JSON.parse(JSON.stringify(response.rows));
				    	var total_purchase = 0;
						var total_sell = 0;
						for (var i = 0; i < result.length; i++) {
					    	total_purchase += result[i].amount << 0;
					    	total_sell += result[i].sell_amount << 0;
						}
					}

                	var sr_no = 0;
                    var my_html = '';    
                    <?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
						my_html += '<form action="<?php echo base_url('import/container/edit/') ?>'+id+'" method="post" id="frmUpdateContainers" enctype="multipart/form-data">';
							my_html += '<div class="modal-body p-0">';
								my_html += '<div class="row col-md-12 m-1">';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Container Booking No</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="booking_no" value="'+response.jobs.cntr_booking_no+'" id="BookingNo">';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Booking Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="booking_date" value="'+response.jobs.cntr_booking_date+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="row">';
											my_html += '<div class="col-md-6">';
												my_html += '<div class="form-group">';
													my_html += '<label class="control-label">Vessel Name</label>';
														my_html += '<input type="text" class="form-control form-control-sm" name="vessel_name" value="'+response.jobs.vessel_name+'" id="VesselName">';
												my_html += '</div>';
											my_html += '</div>';
											my_html += '<div class="col-md-6">';
												my_html += '<div class="form-group">';
													my_html += '<label class="control-label">Vessel Voyage</label>';
														my_html += '<input type="text" class="form-control form-control-sm" name="vessel_voyage" value="'+response.jobs.vessel_voyage+'" id="VesselVoyage">';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';

								my_html += '<div class="row col-md-12 m-1">';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">ETA Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="eta_date" value="'+response.jobs.eta_date+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">ETD Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="etd_date" value="'+response.jobs.etd_date+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Expiry Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="expiry_date" value="'+response.jobs.expiry_date+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';

								my_html += '<div class="row col-md-12 m-1">';
									my_html += '<div class="col-md-3">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Booking Copy</label>';
											my_html += '<input type="file" name="">';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';
								my_html += '<div class="table-responsive modalcustome">';
							        my_html += '<table class="table table-sm table-bordered DataEntry">';
							            my_html += '<thead>';
							            	my_html += '<tr>';
							            		my_html += '<th class="text-center align-middle">##</th>';
							                    my_html += '<th class="text-center align-middle">##</th>';
							                    my_html += '<th class="align-middle">Container No</th>';
							                    my_html += '<th class="align-middle">Size</th>';
							                    my_html += '<th class="align-middle">Container Type</th>';
							                    my_html += '<th class="align-middle">From (Pickup)</th>';
							                    my_html += '<th class="align-middle">To (Delivery)</th>';
							                    my_html += '<th class="align-middle">Vehicle No</th>';
							                    my_html += '<th class="align-middle">Transporter Name</th>';
							                    my_html += '<th class="align-middle">Line Seal</th>';
							                    my_html += '<th class="align-middle">Shipper Seal</th>';
							                    my_html += '<th class="align-middle">Custom Seal</th>';
							                    my_html += '<th class="align-middle d-none">Rcvd. Nett Weight</th>';
							                    my_html += '<th class="text-center align-middle width-80"><a href="#" class="CheckAll"><i class="fa fa-trash-alt"></i></a></th>';

							                my_html += '</tr>';
							            my_html += '</thead>';
										my_html += '<tbody>';
										
										var dnone = "d-none";
										if(response.rows.length){
										    var result = JSON.parse(JSON.stringify(response.rows));
										    var $i;

										    if(result){

											    for(var i=0;i < result.length;i++){
											        var item = result[i]; 
											        
											        if(item.id > 0) {

											        	my_html += '<tr>';
															my_html += '<td class="align-middle text-center"><b>'+(i+1)+'</b></td>';
															my_html += '<td class="align-middle text-center"><b>'+(i+1)+'</b></td>';
															my_html += '<td class="text-center align-middle">';
																my_html += '<input type="hidden" name="id['+item.id+']" value="'+item.id+'">';
																my_html += '<input type="text" class="form-control form-control-sm" name="number['+item.id+']" id="number['+item.id+']" value="'+item.number+'">';
															my_html += '</td>';
															my_html += '<td class="align-middle text-danger text-center">';
																my_html += '<input type="text" class="form-control form-control-sm text-danger text-center ContainerSize" name="size['+item.id+']" value="'+item.size+'">';
															my_html += '</td>';
														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm ContainerType" name="container_type['+item.id+']" value="'+item.container_type+'">';
														    my_html += '</td>';

														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm" name="from['+item.id+']" value="'+item.from+'">';
														    my_html += '</td>';
														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm" name="to['+item.id+']" id="to['+item.id+']" value="'+item.to+'">';
														    my_html += '</td>';
														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm" name="vehicle_no['+item.id+']" id="vehicle_no['+item.id+']" value="'+item.vehicle_no+'">';
														    my_html += '</td>';

														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm TransporterName" name="transporter['+item.id+']" value="'+item.transporter+'">';
														    my_html += '</td>';
														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm" name="line_seal['+item.id+']" value="'+item.line_seal+'" onkeypress="return isNumber(event)">';
														    	my_html += '</td>';
														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm" name="shipper_seal['+item.id+']" value="'+item.shipper_seal+'" onkeypress="return isNumber(event)">';
														    my_html += '</td>';
														    my_html += '<td class="align-middle">';
														    	my_html += '<input type="text" class="form-control form-control-sm" name="custom_seal['+item.id+']" value="'+item.custom_seal+'" onkeypress="return isNumber(event)">';
														    my_html += '</td>';
														    my_html += '<td class="align-middle text-center">'+item.delete_btn;+'</td>';
														my_html += '</tr>';

														sr_no = i+1;
													}
												}

											}
										}
										else
										{
											var dnone = "";
										}

										sr_no++



										my_html += '<tr class="TemplateRow '+dnone+'">';
											my_html += '<td class="align-middle text-center SortHandle"><i class="icon-bars"></i></td>';
											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment text-center" name="new_sr_no[]" value="'+(sr_no)+'"></td>';

											my_html += '<td class="text-center align-middle">';
												my_html += '<input type="text" class="form-control form-control-sm" name="new_number[]" id="new_number[]" value="">';
											my_html += '</td>';
											my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm text-danger text-center ContainerSize" name="new_size[]" value="20"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm ContainerType" name="new_container_type[]"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_from[]"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_to[]" ></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_vehicle_no[]"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm TransporterName" name="new_transporter[]"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_line_seal[]" onkeypress="return isNumber(event)"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_shipper_seal[]" onkeypress="return isNumber(event)"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_custom_seal[]" onkeypress="return isNumber(event)"></td>';
										    my_html += '<td class="align-middle d-none"><input type="text" class="form-control form-control-sm" onkeypress="return isNumberDot(event)" name="new_net_weight[]"></td>';
										    my_html += '<td class="align-middle text-center">';

										    if(dnone != ''){
										 	   my_html += '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';
											}
											else{
												my_html += '<button type="button" class="btn btn-danger btn-sm DelButton" disabled><i class="icon-minus fa fa-minus"></i></button>';	
											}

										    my_html += '</td>';
											my_html += '</tr>';


										my_html += '</tbody>'; 
							        my_html += '</table>';
							    my_html += '</div>';


							function numberWithCommas(x) {
							    return x.toString().split('.')[0].length > 3 ? x.toString().substring(0,x.toString().split('.')[0].length-3).replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + x.toString().substring(x.toString().split('.')[0].length-3): x.toString();
							}

							
							my_html += '<div class="modal-footer">';
								my_html += '<button type="submit" class="btn btn-success AddButton mr-auto"><i class="icon-white icon-plus"></i> New Item</button>';
								my_html += '<button type="button"  onClick="closeContainersModel()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
								my_html += '<button type="submit" class="btn btn-success text-left"><i class="fa fa-save"></i> Update</button>';
							my_html += '</div>';
						my_html += '</form>';

					<?php //} ?>        
                   
					if(my_html)
                        $('#updateContainers').append(my_html);
                    
                    $("#modal-containers").modal('show');
                    $("#loader").hide();
                    $('.select2').select2({
				      theme: 'bootstrap4'
				    })

				    $('.select2-selection__rendered').hover(function () {
					    $(this).removeAttr('title');
					});


				    $('#modal-containers').find('.ContainerSize').kaabar_autocomplete({source: '<?php echo site_url('master/container_type/ajaxSize') ?>', alertText: 'Container Size'});
				    $('#modal-containers').find('.ContainerType').kaabar_autocomplete({source: '<?php echo site_url('master/container_type/ajax') ?>', alertText: 'Container Type'});
				    $('#modal-containers').find('.TransporterName').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/containers/id/transporter') ?>'});

                    ////// CLONE FUNCTION
					function cloneRow(template_row) {

						$new_row = $(template_row).clone();
						$new_row.children('td:last').html(del_button);
						$new_row.removeClass('TemplateRow');
						$new_row.removeClass('d-none');
						$new_row.addClass('lastRowElement');
						$new_row.insertBefore($(template_row));

						$(template_row).find('textarea').each(function(index, el) {
							$new_row.find('textarea').eq(index).val($(this).val());
						});
						
						$(template_row).find('select').each(function(index, el) {
							$new_row.find('select').eq(index).val($(this).val());
						});
						$(template_row).find(':input').not('.Unchanged').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', '');
							else
								$(this).val('');
							
						});

						$(template_row).find('input[type="checkbox"]').not('.Unchanged').prop('checked', '');

						$(template_row).find('td.ClearText').each(function() {
							$(this).text('');
						});
						$(template_row).find('.Increment').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', (parseInt($(this).val(), 10)+1));
							else
								$(this).val(parseInt($(this).val(), 10)+1);
						});
						$(template_row).find('.DataDefault').each(function() {
							$(this).val($(this).attr('data-default'));
						});

						$new_row.find('.ContainerSize').kaabar_autocomplete({source: '<?php echo site_url('master/container_type/ajaxSize') ?>', alertText: 'Container Size'});
				    	$new_row.find('.ContainerType').kaabar_autocomplete({source: '<?php echo site_url('master/container_type/ajax') ?>', alertText: 'Container Type'});
				    	$new_row.find('.TransporterName').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/containers/id/transporter') ?>'});

				    	$(template_row).find(':input.Focus').focus();
					}

					////// CLONE NEW ROW
					$('#modal-containers').unbind().on('click', 'button.AddButton', function(e) {

						var submit_row = $(this).hasClass('RowSubmit');
						var clone_row  = true;
						$template_row  = $(this).parents('#updateContainers').find('table.DataEntry').find('tr.TemplateRow');
						$validate_row  = $(this).parents('#updateContainers').find('table.DataEntry').find('tr.lastRowElement');

						$validate_row.find('.Validate').each(function(index, el) {
							if ($(this).val() === '') {
								//console.log($(this).attr('class'));
								clone_row = false;
								$(this).addClass('is-invalid');
							}
							else {
								$(this).removeClass('is-invalid');
							}
						});

						if (clone_row) {
							
							cloneRow($template_row);

							//$(this).parents('#updateContainers').find('table.DataEntry').find('tr.lastRowElement');

							if (submit_row === false) {
								e.preventDefault();
							}
						}
						else {
							e.preventDefault();
						}
					});

					// Data Entry +/-
					var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';

					////// Delete Attachment
					$('table.DataEntry').on('click', 'a.deleteAttach', function(event) {
						var job_id = id;
						var itemrow_id = $(this).parents('tr').find('.ItemRowID').val();
						Swal.fire({
		                	title: "Are you sure?",
							text: "You want to delete this attachement..!",
							icon: "warning",
							allowOutsideClick: false,
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {
		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		event.preventDefault();
					       		event.stopPropagation();
		                	}
		                	else if (result.value) {
		                		$.ajax({
								  	url: '<?php echo site_url('import') ?>/costsheet/deleteattach',
								  	type: 'POST',
								  	data: { job_id : job_id, row_id : itemrow_id },
								  	dataType: 'json',
								  	beforeSend: function(){
							            $("#loader").show();
							        },
								  	success:function(response) {
								  		if(response.success === true) {
								  			$("#loader").hide();
								  			Swal.fire({
										        title: 'Wow...!',
										        html:  ""+response.messages+"",
										        icon: "success",
										    }).then(function(result) {
										    	$('#modal-costsheet').find('#updateCostsheet').html('');
										    	updateCostsheet(job_id);
										    });
										} 
										else 
										{
											$("#loader").hide();
										  	Swal.fire({
										        title: 'Opps...!',
										        html:  ""+response.messages+"",
										        icon: "error",
										    });
										}
										return false;
									}
								}); 
		                	}
		                });
		                event.preventDefault();
					    event.stopPropagation();

					});

					////// New Added Row Remove Confirmation
					$('table.DataEntry').on('click', 'button.DelButton', function() {
						const row = $(this).parents('tr');
						const that = this // here a is change
			            var output = true;
		                Swal.fire({
		                	title: "Are you sure?",
							text: "You won't be able to revert this!",
							icon: "warning",
							allowOutsideClick: false,
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {

		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		output = false;
		                        return false;
		                	}
		                	else if (result.value) {
								row.remove();		
		                	}
		                });
			        });

					////// Focus On Select All 
				    $("input[type='text']").click(function () {
					   $(this).select();
					});

				    ////// Existing Row Remove Confirmation
				    $('.DataEntry').on('change', '.DeleteCheckbox', function() {
						const row = $(this).closest('tr');
						const that = this // here a is change
			            if ($(this).is(":checked")){
			                var output = true;
			                 Swal.fire({
			                	title: "Are you sure?",
								text: "You won't be able to revert this!",
								icon: "warning",
								allowOutsideClick: false,
								showCancelButton: true,
								confirmButtonColor: "#3085d6",
								cancelButtonColor: "#d33",
								confirmButtonText: "Yes, Delete it...!",
								cancelButtonText: "Cancel",
								buttonsStyling: true
			                }).then(function(result) {

			                	if (result.dismiss === Swal.DismissReason.cancel) {
			                		that.checked= false;// here is a change
			                        output = false;
			                	}
			                	else if (result.value) {
									if (!that.checked)
							            row.fadeIn('slow');
							        else 
							            row.fadeOut('slow');	
			                	}
			                });
			            }
			        });

			        /////// Submit Form Data
                    $("#frmUpdateContainers").unbind('submit').on('submit', function() {

                    	//$(this).validate();
                    	var formData = new FormData(this);

                    	//var form = $(this);
                    	
                    	// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: $(this).attr('action'),
						  	type: $(this).attr('method'),
						  	data: formData, // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	contentType: false,
    						processData: false,
    						beforeSend: function(){
					            $("#loader").show();
					        },
						  	success:function(response) {
						  		closeContainersModel();
						  		if(response.success === true) {
						  			$("#loader").hide();
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    });
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								    $("#loader").hide();
								  } 
								  else 
								  {
								  	$("#loader").hide();
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
								return false;
							}
						}); 
						return false;
					});
                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
        	Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

    function closeContainersModel(){
    	$('#modal-containers').modal('hide');
    	$('#modal-containers').find('#updateContainers').html('');
    }

    function updateCostsheet(id = null)
    {   
    	
    	var id = $("input[name=id]").val();

        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>import/costsheet/get/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.rows.length){
				    	var result = JSON.parse(JSON.stringify(response.rows));
				    	var total_purchase = 0;
						var total_sell = 0;
						for (var i = 0; i < result.length; i++) {
					    	total_purchase += result[i].amount << 0;
					    	total_sell += result[i].sell_amount << 0;
						}
					}

                	var sr_no = 0;
                    var my_html = '';    
                    <?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
						my_html += '<form action="<?php echo base_url('import/costsheet/edit/') ?>'+id+'" method="post" id="frmUpdateCostsheet" enctype="multipart/form-data">';
							my_html += '<div class="modal-body p-0">';
								my_html += '<div class="table-responsive modalcustome">';
							        my_html += '<table class="table table-sm table-bordered DataEntry">';
							            my_html += '<thead>';
							            	my_html += '<tr>';
							                    my_html += '<th class="text-center align-middle width-50" rowspan="2"></th>';
							                    my_html += '<th class="text-center align-middle width-50" rowspan="2">##</th>';
							                    my_html += '<th class="align-middle width-200" rowspan="2">Charge Head</th>';
							                    my_html += '<th class="align-middle width-200" rowspan="2">Vendor Name</th>';
							                    my_html += '<th class="text-center align-middle" colspan="7">Buy (Inward)</th>';
							                    my_html += '<th class="text-center align-middle" colspan="7">Sell (Outward)</th>';
							                    my_html += '<th class="text-center align-middle width-200" rowspan="2">Doc Upload</th>';
							                    my_html += '<th class="text-center align-middle width-80" rowspan="2"><a href="#" class="CheckAll"><i class="fa fa-trash-alt"></i></a></th>';
							                my_html += '</tr>';
							                my_html += '<tr>';
							                    my_html += '<th class="align-middle text-center width-80">Currancy</th>';
							                    my_html += '<th class="align-middle text-center width-80">EX Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">INR</th>';
							                    my_html += '<th class="align-middle text-center width-80">Unit</th>';
							                    my_html += '<th class="align-middle text-center width-80">Qty</th>';
							                    my_html += '<th class="align-middle text-center width-80">Amt</th>';
							                    
							                    my_html += '<th class="align-middle text-center width-80">Currancy</th>';
							                    my_html += '<th class="align-middle text-center width-80">EX Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">INR</th>';
							                    my_html += '<th class="align-middle text-center width-80">Unit</th>';
							                    my_html += '<th class="align-middle text-center width-80">Qty</th>';
							                    my_html += '<th class="align-middle text-center width-80">Amt</th>';
							                    
							                my_html += '</tr>';
							            my_html += '</thead>';
										my_html += '<tbody>';
										
										var dnone = "d-none";
										if(response.rows.length){
										    var result = JSON.parse(JSON.stringify(response.rows));
										    var $i;

										    if(result){

											    for(var i=0;i < result.length;i++){
											        var item = result[i]; 
											        
											        if(item.id > 0) {


											        	console.log(item);

											        	var $non_stax = '';
											        	if(item.stax_code)
															if (item.stax_code.length == 0)
																$non_stax = 'NonSTax';

														//var total = round(item.amount, 2);
														
														my_html += '<tr>';
														my_html += '<td class="align-middle text-center grayLight '+(item.category === 'Bill Items' ? 'SortHandle' : 'ui-state-disabled')+'"><i class="icon-bars"></i></td>';
														if (item.category == 'Bill Items') 
															my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Validate text-center" name="kbr_sr_no['+item.id+']" value="'+item.sr_no+'" required></td>';
														else
															my_html += '<td class="align-middle '+$non_stax+'"><input type="hidden" class="form-control form-control-sm Numeric Validate text-center" name="kbr_sr_no['+item.id+']" value="0"></td>';

														my_html += '<td class="'+$non_stax+'"><input type="hidden" class="form-control form-control-sm BillItemID" name="kbr_bill_item_id['+item.id+']" value="'+item.bill_item_id+'">';
														my_html += '<input type="text" class="form-control form-control-sm BillItemCode" value="'+item.particulars+'" name="kbr_bill_item_code['+item.id+']" required></td>';
														my_html += '<td class="align-middle '+$non_stax+'">';

														my_html += '<input type="text" class="form-control form-control-sm VendorName" value="'+item.vendor_name+'" required>';
														my_html += '<input type="hidden" class="form-control form-control-sm" name="kbr_vendor_id['+item.id+']" value="'+item.vendor_id+'" required></td>'
														
														////// Start Purchase Section
														my_html += '<td class="align-middle '+$non_stax+'">';
															my_html += item.is_inr;
															my_html += '<input type="text" class="Autocomplete form-control form-control-sm CurrencyName Unchanged Validate" value="'+item.currency_name+'" name="kbr_currency_name['+item.id+']" required>';
															my_html += '<input type="hidden" class="Unchanged CurrencyNameId" value="'+item.currency_id+'" name="kbr_currency['+item.id+']">'
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'">';
															my_html += '<input type="text" class="form-control form-control-sm Unchanged ExchangeRate Validate text-right" value="'+item.ex_rate+'" name="kbr_ex_rate['+item.id+']" required>';
															my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric Currency text-right" name="kbr_currency_amount['+item.id+']" value="'+item.currency_amt+'" onkeypress="return isNumberDot(event)" >';
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Rate" name="kbr_rate['+item.id+']" 	value="'+item.rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm InrRate Numeric" name="kbr_inr_rate['+item.id+']" readonly value="'+item.inr_rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'">'+item.unit_id+'</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Units Validate" name="kbr_units['+item.id+']" value="'+item.qty+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Amount Validate" name="kbr_amount['+item.id+']" readonly value="'+item.amount+'" required></td>';
														// ////// END Purchase Section

														// ////// Start Sell Section
														my_html += '<td class="align-middle '+$non_stax+'">';

															my_html += item.sell_is_inr;
															my_html += '<input type="text" class="form-control form-control-sm SellCurrencyName Unchanged Validate" value="'+item.sell_currency_name+'" name="kbr_sell_currency_name['+item.id+']" required>';
															my_html += '<input type="hidden" class="SellCurrencyNameId Unchanged" value="'+item.sell_currency_id+'" name="kbr_sell_currency['+item.id+']">'
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'">';
															my_html += '<input type="text" class="form-control form-control-sm SellExchangeRate Unchanged Validate text-right" value="'+item.sell_ex_rate+'" name="kbr_sell_ex_rate['+item.id+']" required>';
															my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric SellCurrency text-right" name="kbr_sell_currency_amount['+item.id+']" value="'+item.sell_currency_amt+'" onkeypress="return isNumberDot(event)">';
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm  Numeric SellRate" name="kbr_sell_rate['+item.id+']" value="'+item.sell_rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm SellInrRate Numeric" name="kbr_sell_inr_rate['+item.id+']" readonly value="'+item.sell_inr_rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'">'+item.sell_unit_id+'</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric SellUnits Validate" name="kbr_sell_units['+item.id+']" value="'+item.sell_qty+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric SellAmount Validate" name="kbr_sell_amount['+item.id+']" readonly value="'+item.sell_amount+'" required></td>';
														////// End Sell Section
														if(item.upfile != null){
															//my_html += '<td class="align-middle '+$non_stax+'"><input type="file" class="Fileupload d-none" name="kbr_upload['+item.id+']"></td>';
															my_html += '<td class="align-middle text-center '+$non_stax+'">';
															my_html += '<input type="hidden" class="ItemRowID" name="kbr_row_id['+item.id+']" value="'+item.id+'">';

															my_html += '<a href="'+item.upfile+'" download><i class="fa fa-download fa-lg text-success"></i></a>';
															my_html += '<a href="" class="deleteAttach"><i class="fa fa-trash-alt fa-lg text-danger ml-2"></i></a></td>';
															my_html += '</td>';
														}
														else
														{



															my_html += '<td class="align-middle '+$non_stax+'"><input type="file" class="Fileupload" name="kbr_upload['+item.id+']"></td>';
														}

														my_html += "<td class='align-middle text-center'"+$non_stax+"'>";
															my_html +=  item.delete_btn;
														my_html += "</td>";

														my_html += '</tr>';

														sr_no = item.sr_no;
													}
												}

											}
										}
										else
										{
											var dnone = "";
										}

										sr_no++
										my_html += '<tr class="TemplateRow '+dnone+'">';
											my_html += '<td class="align-middle text-center SortHandle"><i class="icon-bars"></i></td>';
											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment text-center" name="new_sr_no[]" value="'+(sr_no)+'"></td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm BillItemCode Focus" value="" name="new_bill_item_code[]" >';
												my_html += '<input type="hidden" class="form-control form-control-sm BillItemID" name="new_bill_item_id[]" title="id" value="">';
											my_html += '</td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm VendorName" value="" >';
												my_html += '<input type="hidden" class="form-control form-control-sm" name="new_vendor_id[]" title="id" value="" >'
											my_html += '</td>';
											////// Start Purchase Section
											my_html += '<td>';
												my_html += '<?php echo form_dropdown_single('new_is_inr[]', array('Yes'=>'Yes', 'No'=>'No'), 'Yes', 'class="form-control form-control-sm Unchanged IsINR d-none"') ?>';

												my_html += '<input type="text" class="form-control form-control-sm CurrencyName Unchanged Validate" value="INR - Indian Rupees" name="new_currency_name[]" >';
												my_html += '<input type="hidden" class="Unchanged CurrencyNameId" value="1" name="new_currency[]">'
											my_html += '</td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm Unchanged ExchangeRate Validate text-right" value="1" name="new_ex_rate[]" >';
												my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric Currency text-right" name="new_currency_amount[]" value="1" onkeypress="return isNumberDot(event)" >';
											my_html += '</td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Rate" name="new_rate[]" 	value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm InrRate Numeric" name="new_inr_rate[]" readonly value="" ></td>';

											my_html += '<td><?php echo form_dropdown_single('new_unit[]', getSelectOptions('units'), '', 'class="Unchanged form-control form-control-sm Unit"') ?></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Units Validate" name="new_units[]" value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Amount Validate" name="new_amount[]" readonly value="" ></td>';
											////// END Purchase Section

											////// Start Sell Section
												my_html += '<td>';
												my_html += '<?php echo form_dropdown_single('new_sell_is_inr[]', array('Yes'=>'Yes', 'No'=>'No'), 'Yes', 'class="form-control form-control-sm Unchanged SellIsINR d-none"') ?>';

												my_html += '<input type="text" class="form-control form-control-sm SellCurrencyName Unchanged Validate" value="INR - Indian Rupees" name="new_sell_currency_name[]" >';
												my_html += '<input type="hidden" class="SellCurrencyNameId Unchanged" value="1" name="new_sell_currency[]">'
											my_html += '</td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm SellExchangeRate Unchanged Validate text-right" value="1" name="new_sell_ex_rate[]" >';
												my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric SellCurrency text-right" name="new_sell_currency_amount[]" value="1" onkeypress="return isNumberDot(event)">';
											my_html += '</td>';

											my_html += '<td><input type="text" class="form-control form-control-sm  Numeric SellRate" name="new_sell_rate[]" value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm SellInrRate Numeric" name="new_sell_inr_rate[]" readonly value="" ></td>';

											my_html += '<td><?php echo form_dropdown_single('new_sell_unit[]', getSelectOptions('units'), '', 'class="form-control form-control-sm SellUnit Unchanged"') ?></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric SellUnits Validate" name="new_sell_units[]" value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric SellAmount Validate" name="new_sell_amount[]" readonly value="" ></td>';
											////// End Sell Section
											my_html += '<td><input type="file" class="" name="new_upload[]"></td>';
											my_html += '<td class="align-middle text-center"><button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button></td>';
											//my_html += '<td class="align-middle text-center"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>';
											my_html += '</tr>';


										my_html += '</tbody>'; 
							        my_html += '</table>';
							    my_html += '</div>';


							function numberWithCommas(x) {
							    return x.toString().split('.')[0].length > 3 ? x.toString().substring(0,x.toString().split('.')[0].length-3).replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + x.toString().substring(x.toString().split('.')[0].length-3): x.toString();
							}

							my_html += '</div>';
						    my_html += '<div class="modal-body">';
						    	my_html += '<div class="row">';
						    		my_html += '<div class="col-md-6">';
						    			my_html += '<div class="row">';
						    				my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-danger"><b>Total Purchase ( Inword) :</b></h5>';
											my_html += '</div>';
											my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-danger"><b><span id="TotalPurchase">';
													if(total_purchase > 0)
														my_html += numberWithCommas(total_purchase.toFixed(2));	
													else
														my_html += '0.00';	

												my_html += '</span></b></h5>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-6">';
						    			my_html += '<div class="row">';
						    				my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-success"><b>Total Sell (Ourward) :</b></h5>';
											my_html += '</div>';
											my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-success"><b><span id="TotalSell">';
													if(total_sell > 0)
														my_html += numberWithCommas(total_sell.toFixed(2));	
													else
														my_html += '0.00';	

												my_html += '</span></b></h5>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';
							my_html += '</div>';
							my_html += '<div class="modal-footer">';
								my_html += '<button type="submit" class="btn btn-success AddButton mr-auto"><i class="icon-white icon-plus"></i> New Item</button>';
								my_html += '<button type="button" onClick="closeCostsheetModel()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
								my_html += '<button type="submit" class="btn btn-success text-left"><i class="fa fa-save"></i> Update</button>';
							my_html += '</div>';
						my_html += '</form>';

					<?php //} ?>        
                   
					if(my_html)
                        $('#updateCostsheet').append(my_html);
                    
                    $("#modal-costsheet").modal('show');
                    $("#loader").hide();
                    ////// CLONE FUNCTION
					function cloneRow(template_row) {

						$new_row = $(template_row).clone();
						$new_row.children('td:last').html(del_button);
						$new_row.removeClass('TemplateRow');
						$new_row.removeClass('d-none');
						$new_row.addClass('lastRowElement');
						$new_row.insertBefore($(template_row));

						$(template_row).find('textarea').each(function(index, el) {
							$new_row.find('textarea').eq(index).val($(this).val());
						});
						
						$(template_row).find('select').each(function(index, el) {
							$new_row.find('select').eq(index).val($(this).val());
						});
						$(template_row).find(':input').not('.Unchanged').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', '');
							else
								$(this).val('');
							
						});

						$(template_row).find('input[type="checkbox"]').not('.Unchanged').prop('checked', '');

						$(template_row).find('td.ClearText').each(function() {
							$(this).text('');
						});
						$(template_row).find('.Increment').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', (parseInt($(this).val(), 10)+1));
							else
								$(this).val(parseInt($(this).val(), 10)+1);
						});
						$(template_row).find('.DataDefault').each(function() {
							$(this).val($(this).attr('data-default'));
						});

						$(template_row).find(':input.Focus').focus();

						$new_row.find('.BillItemCode').cskaabar_autocomplete({source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>'});
						$new_row.find('.VendorName').kaabar_autocomplete({source: '<?php echo site_url('master/operation/vendor/ajax') ?>'});
						$new_row.find('.CurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.IsINR'});
						$new_row.find('.SellCurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.SellIsINR'});
					}

					////// CLONE NEW ROW
					$('#modal-costsheet').unbind().on('click', 'button.AddButton', function(e) {

						var submit_row = $(this).hasClass('RowSubmit');
						var clone_row  = true;
						$template_row  = $(this).parents('#updateCostsheet').find('table.DataEntry').find('tr.TemplateRow');
						$validate_row  = $(this).parents('#updateCostsheet').find('table.DataEntry').find('tr.lastRowElement');

						$validate_row.find('.Validate').each(function(index, el) {
							if ($(this).val() === '') {
								//console.log($(this).attr('class'));
								clone_row = false;
								$(this).addClass('is-invalid');
							}
							else {
								$(this).removeClass('is-invalid');
							}
						});

						if (clone_row) {
							
							cloneRow($template_row);
							
							if (submit_row === false) {
								e.preventDefault();
							}
						}
						else {
							e.preventDefault();
						}
					});

					///////// Autocomplete Bill Item
     				$('.BillItemCode').cskaabar_autocomplete({source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>'});
     				///////// Autocomplete Vendor Name
					$('.VendorName').kaabar_autocomplete({source: '<?php echo site_url('master/operation/vendor/ajax') ?>'});
					
					/////// PURCHASE SECTION
					///////// Autocomplete Purchase Currancy Name
					$('.CurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.IsINR'});

					// Data Entry +/-
					var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';

					////// Delete Attachment
					$('table.DataEntry').on('click', 'a.deleteAttach', function(event) {
						var job_id = id;
						var itemrow_id = $(this).parents('tr').find('.ItemRowID').val();
						Swal.fire({
		                	title: "Are you sure?",
							text: "You want to delete this attachement..!",
							icon: "warning",
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {
		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		event.preventDefault();
					       		event.stopPropagation();
		                	}
		                	else if (result.value) {
		                		$.ajax({
								  	url: '<?php echo site_url('import') ?>/costsheet/deleteattach',
								  	type: 'POST',
								  	data: { job_id : job_id, row_id : itemrow_id },
								  	dataType: 'json',
								  	beforeSend: function(){
							            $("#loader").show();
							        },
								  	success:function(response) {
								  		if(response.success === true) {
								  			$("#loader").hide();
								  			Swal.fire({
										        title: 'Wow...!',
										        html:  ""+response.messages+"",
										        icon: "success",
										    }).then(function(result) {
										    	$('#modal-costsheet').find('#updateCostsheet').html('');
										    	updateCostsheet(job_id);
										    });
										} 
										else 
										{
											$("#loader").hide();
										  	Swal.fire({
										        title: 'Opps...!',
										        html:  ""+response.messages+"",
										        icon: "error",
										    });
										}
										return false;
									}
								}); 
		                	}
		                });
		                event.preventDefault();
					    event.stopPropagation();

					});

					////// New Added Row Remove Confirmation
					$('table.DataEntry').on('click', 'button.DelButton', function() {
						const row = $(this).parents('tr');
						const that = this // here a is change
			            var output = true;
		                Swal.fire({
		                	title: "Are you sure?",
							text: "You won't be able to revert this!",
							icon: "warning",
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {

		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		output = false;
		                        return false;
		                	}
		                	else if (result.value) {
								row.remove();		
		                	}
		                });
			        });

					////// Focus On Select All 
				    $("input[type='text']").click(function () {
					   $(this).select();
					});

				    ////// Existing Row Remove Confirmation
				    $('.DataEntry').on('change', '.DeleteCheckbox', function() {
						const row = $(this).closest('tr');
						const that = this // here a is change
			            if ($(this).is(":checked")){
			                var output = true;
			                 Swal.fire({
			                	title: "Are you sure?",
								text: "You won't be able to revert this!",
								icon: "warning",
								showCancelButton: true,
								confirmButtonColor: "#3085d6",
								cancelButtonColor: "#d33",
								confirmButtonText: "Yes, Delete it...!",
								cancelButtonText: "Cancel",
								buttonsStyling: true
			                }).then(function(result) {

			                	if (result.dismiss === Swal.DismissReason.cancel) {
			                		that.checked= false;// here is a change
			                        output = false;
			                	}
			                	else if (result.value) {
									if (!that.checked)
							            row.fadeIn('slow');
							        else 
							            row.fadeOut('slow');	
			                	}
			                });
			            }
			        });

				   $('.DataEntry').on('keyup', '.ExchangeRate', function() {

						var ex_rate  = $(this).val();
						var unit     = $(this).parents('tr').children('td').find('.Units').val();
						var is_inr   = $(this).parents('tr').children('td').find('.IsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.Rate').val();
						var currency = $(this).parents('tr').children('td').find('.Currency');
						var inr_rate = $(this).parents('tr').children('td').find('.InrRate');
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						
						$(this).parents('tr').children('td').find('.Amount').val(amount);
						getTotal();
					});

					$('.DataEntry').on('keyup', '.Units', function() {
						var ex_rate  = $(this).parents('tr').children('td').find('.ExchangeRate').val();
						var unit     = $(this).val();
						var is_inr   = $(this).parents('tr').children('td').find('.IsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.Rate').val();
						var currency = $(this).parents('tr').children('td').find('.Currency');
						var inr_rate = $(this).parents('tr').children('td').find('.InrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.Amount').val(amount);
						$(this).parents('tr').children('td').find('.SellUnits').val(unit);
						getTotal();
					});

					$('.DataEntry').on('keyup', '.Rate', function() {
						var ex_rate = $(this).parents('tr').children('td').find('.ExchangeRate').val();
						var rate    = $(this).val();
						var is_inr  = $(this).parents('tr').children('td').find('.IsINR').val();
						var unit    = $(this).parents('tr').children('td').find('.Units').val();
						var currency = $(this).parents('tr').children('td').find('.Currency');
						var inr_rate = $(this).parents('tr').children('td').find('.InrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.Amount').val(amount);
						getTotal();
					});
					function getTotal(){
						var total_amount = 0;
					    $(".Amount").each(function(){
					        total_amount += +$(this).val();
					    });

					    function numberWithCommas(x) {
						    return x.toString().split('.')[0].length > 3 ? x.toString().substring(0,x.toString().split('.')[0].length-3).replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + x.toString().substring(x.toString().split('.')[0].length-3): x.toString();
						}

					    $("#TotalPurchase").html(numberWithCommas(total_amount.toFixed(2)));	
					}
					///////// END PURCHASE SECTION						

					/////// SALE SECTION
					///////// Autocomplete Sale Currancy Name
					$('.SellCurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.SellIsINR'});

					$('.DataEntry').on('keyup', '.SellExchangeRate', function() {

						var ex_rate  = $(this).val();
						var unit     = $(this).parents('tr').children('td').find('.SellUnits').val();
						var is_inr   = $(this).parents('tr').children('td').find('.SellIsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.SellRate').val();
						var currency = $(this).parents('tr').children('td').find('.SellCurrency');
						var inr_rate = $(this).parents('tr').children('td').find('.SellInrRate');
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.SellAmount').val(amount);
						getSellTotal();
					});

					$('.DataEntry').on('keyup', '.SellUnits', function() {
						var ex_rate  = $(this).parents('tr').children('td').find('.SellExchangeRate').val();
						var unit     = $(this).val();
						var is_inr   = $(this).parents('tr').children('td').find('.SellIsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.SellRate').val();
						var currency = $(this).parents('tr').children('td').find('.SellCurrency');
						var inr_rate = $(this).parents('tr').children('td').find('.SellInrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.SellAmount').val(amount);
						getSellTotal();
					});

					$('.DataEntry').on('keyup', '.SellRate', function() {
						var ex_rate = $(this).parents('tr').children('td').find('.SellExchangeRate').val();
						var rate    = $(this).val();
						var is_inr  = $(this).parents('tr').children('td').find('.SellIsINR').val();
						var unit    = $(this).parents('tr').children('td').find('.SellUnits').val();
						var currency = $(this).parents('tr').children('td').find('.SellCurrency');
						var inr_rate = $(this).parents('tr').children('td').find('.SellInrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.SellAmount').val(amount);
						getSellTotal();
					});

					function getSellTotal(){
						var total_amount = 0;
					    $(".SellAmount").each(function(){
					        total_amount += +$(this).val();
					    });

					    function numberWithCommas(x) {
						    return x.toString().split('.')[0].length > 3 ? x.toString().substring(0,x.toString().split('.')[0].length-3).replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + x.toString().substring(x.toString().split('.')[0].length-3): x.toString();
						}

					    $("#TotalSell").html(numberWithCommas(total_amount.toFixed(2)));	
					}
					/////// END SALE SECTION
					
					/////// Submit Form Data
                    $("#frmUpdateCostsheet").unbind('submit').on('submit', function() {

                    	//$(this).validate();
                    	var formData = new FormData(this);

                    	//var form = $(this);
                    	
                    	// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: $(this).attr('action'),
						  	type: $(this).attr('method'),
						  	data: formData, // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	contentType: false,
    						processData: false,
    						beforeSend: function(){
					            $("#loader").show();
					        },
						  	success:function(response) {
						  		closeCostsheetModel();
						  		if(response.success === true) {
						  			$("#loader").hide();
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    });
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								    $("#loader").hide();
								  } 
								  else 
								  {
								  	$("#loader").hide();
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
								return false;
							}
						}); 
						return false;
					});
                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
            $("#modal-containers").modal('hide');
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

    function closeCostsheetModel(){
    	$('#modal-costsheet').modal('hide');
    	$('#modal-costsheet').find('#updateCostsheet').html('');
    }


    function attachDocuments(id = null)
    {   
    	var id = $("input[name=id]").val();
        
        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>import/job_document/get/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {
                    
                    var dnone = "d-none";
                	var sr_no = 0;
                    var my_html = '';    

					<?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
						my_html += '<form action="<?php echo base_url('import/job_document/attach/') ?>'+id+'" method="post" enctype=" multipart/form-data" id="importDocument">';
							my_html += '<div class="modal-body">';
								my_html += '<div class="table-responsive  table-sm">';
							        my_html += '<table class="table table-sm table-bordered DataEntry">';
							            my_html += '<thead>';
							                my_html += '<tr>';
							                    my_html += '<th class="text-center align-middle">Sr</th>';
							                    my_html += '<th width="35%" class="align-middle">Document Name</th>';
							                    my_html += '<th class="align-middle">Remarks</th>';
							                    my_html += '<th class="align-middle">Date</th>';
							                    my_html += '<th width="15%" class="align-middle">Upload</th>';
							                    my_html += '<th class="text-center align-middle width-80"><a href="#" class="CheckAll"><i class="fa fa-trash-alt"></i></a></th>';
							                my_html += '</tr>';
							            my_html += '</thead>';
							            my_html += '<tbody>';

							            	
							            	if(response.files.length){
							            		
							            		var dnone = "";

							                    var result = JSON.parse(JSON.stringify(response.files));
							                    var $i;

							                    for(var i=0;i < result.length;i++){
							                        var item = result[i]; 

							                        if(item.id > 0) {
							                        	my_html += '<tr>';
							                        		my_html += '<td class="align-middle text-center"><b>'+(i+1)+'</b></td>';
								                        	my_html += '<td class="align-middle">';
								                                my_html += '<input type="hidden" class="form-control form-control-sm DocId"  name="doc_id['+item.id+']" value="'+item.id+'">';
								                                my_html += '<input type="text" class="form-control form-control-sm"  name="doc_name['+item.id+']" value="'+item.doc_name+'"></td>';
								                            my_html += '<td class="align-middle"><input type="text" name="doc_remark['+item.id+']" class="form-control form-control-sm"  value="'+item.doc_remark+'"></td>';
								                            my_html += '<td class="text-center align-middle">'+item.created_on+'</td>';
								                            my_html += '<td>';
																my_html += '<input type="file" class="d-none" name="file['+item.id+']">';
																my_html += '<a href="'+item.filepath+'" download><i class="fa fa-download fa-lg text-success"></i> '+item.filename+'</a>';
															my_html += '</td>';
								                            my_html += '<td class="align-middle text-center">'+item.deleteBtn;+'</td>';
								                        my_html += '</tr>';

								                        sr_no = i+1;
								                    }
							                    }
							                }
							                else
							                {
							                   var dnone = "";
							                }
							                sr_no++

							                my_html += '<tr class="TemplateRow '+dnone+'">';
							                my_html += '<td class="text-center">'+sr_no+'</td>';
											my_html += '<td class="align-middle">';
				                                my_html += '<input type="text" class="form-control form-control-sm Validate Focus" title="Document Name Required" name="new_doc_name[]">';
				                            my_html += '</td>';
				                            my_html += '<td><input type="text" class="form-control form-control-sm" name="new_doc_remark[]"></td>';
				                            my_html += '<td class="text-center"></td>';
				                            my_html += '<td><input type="file" class="Validate" title="Document File Required" name="new_file[]" value=""></td>';
				                            my_html += '<td class="align-middle text-center">';
				                            if(dnone != ''){
										 	   my_html += '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';
											}
											else{
												my_html += '<button type="button" class="btn btn-danger btn-sm DelButton" disabled><i class="icon-minus fa fa-minus"></i></button>';	
											}
											my_html += '</td>';
					                        my_html += '</tr>';

							            my_html += '</tbody>'; 
							        my_html += '</table>';
							    my_html += '</div>';
						    my_html += '</div>';

							my_html += '<div class="modal-footer">';
							my_html += '<button type="submit" class="btn btn-success AddButton mr-auto"><i class="icon-white icon-plus"></i> New Item</button>';
								my_html += '<button type="button" onClick="closeDocumentModel()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
								my_html += '<button type="submit" class="btn btn-success text-left"><i class="fa fa-save"></i> Update</button>';
							my_html += '</div>';
						my_html += '</form>';

					<?php //} ?>        
                   
					if(my_html)
                        $('#documentUpload').append(my_html);
                    
                    $("#modal-import-document").modal('show');

                    $("#loader").hide();

                    $('.select2').select2({
				      theme: 'bootstrap4'
				    })

				    $('.select2-selection__rendered').hover(function () {
					    $(this).removeAttr('title');
					});

                    //$("#loader").hide();
                    function cloneRow(template_row) {

						$new_row = $(template_row).clone();
						$new_row.children('td:last').html(del_button);
						$new_row.removeClass('TemplateRow');
						$new_row.removeClass('d-none');
						$new_row.addClass('lastRowElement');
						$new_row.insertBefore($(template_row));

						$(template_row).find('textarea').each(function(index, el) {
							$new_row.find('textarea').eq(index).val($(this).val());
						});
						
						$(template_row).find('select').each(function(index, el) {
							$new_row.find('select').eq(index).val($(this).val());
						});
						$(template_row).find(':input').not('.Unchanged').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', '');
							else
								$(this).val('');
							
						});

						$(template_row).find('input[type="checkbox"]').not('.Unchanged').prop('checked', '');

						$(template_row).find('td.ClearText').each(function() {
							$(this).text('');
						});
						$(template_row).find('.Increment').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', (parseInt($(this).val(), 10)+1));
							else
								$(this).val(parseInt($(this).val(), 10)+1);
						});
						$(template_row).find('.DataDefault').each(function() {
							$(this).val($(this).attr('data-default'));
						});

						$(template_row).find(':input.Focus').focus();
					}

                    $('#modal-import-document').unbind().on('click', 'button.AddButton', function(e) {
                    	

						var submit_row = $(this).hasClass('RowSubmit');
						var clone_row  = true;
						$template_row  = $(this).parents('#documentUpload').find('table.DataEntry').find('tr.TemplateRow');
						$validate_row  = $(this).parents('#documentUpload').find('table.DataEntry').find('tr.lastRowElement');

						var arr = [];
						$template_row.find('.Validate').each(function(index, el) {
							if ($(this).val() === '') {

								clone_row = false;
								var message = $(this).attr('title');
						    	arr.push(message);
						    }
							
						});

						if(arr.length){
							/// Alert If Validation Failed
						    new PNotify({
				      			text: arr.join(", \n"),
								type: 'error',
								nonblock: {
									nonblock: true,
									nonblock_opacity: .2
								}
							});
						}	

						if (clone_row) {
							
							cloneRow($template_row);
							
							if (submit_row === false) {
								e.preventDefault();
							}
						}
						else {
							e.preventDefault();
						}
					});

                    var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';
                    // submit the edit from 
                    $("#importDocument").unbind('submit').bind('submit', function() {
                        
                        var formData = new FormData(this);

                        // // remove the text-danger
                        $(".text-danger").remove();

                        $.ajax({
                            url: $(this).attr('action'),
						  	type: $(this).attr('method'),
						  	data: formData, // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	contentType: false,
    						processData: false,
                            beforeSend: function(){
                                $("#loader").show();
                            },
                            success:function(response) {
                            	//$("#modal-import-document").modal('hide');
                            	var arr = [];
                            	var arr1 = [];
                            	if(response.success === true) {
                            		$.each(response.messages, function(index, value) {
                            			if(value.success == true)
						  					arr.push(value.message)
						  				else
						  					arr1.push(value.message)
						  			})
                            		$("#loader").hide();
						  			
						  			/// Alert If Validation Failed
						  			if(arr.length != 0){
									    new PNotify({
							      			text: arr.join(" \n"),
											type: 'success',
											nonblock: {
												nonblock: true,
												nonblock_opacity: .2
											}
										});
									}

									 /// Alert If Validation Failed
								    if(arr1.length != 0){
									    new PNotify({
							      			text: arr1.join(" \n"),
											type: 'error',
											nonblock: {
												nonblock: true,
												nonblock_opacity: .2
											}
										});
									}	

									$('#modal-import-document').find('#documentUpload').html('');
									attachDocuments(id)
						  			
								} 
								else 
								{

								  $("#loader").hide();
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								
								return false;
							}
                        }); 
                        return false;
                    });

					////// New Added Row Remove Confirmation
					$('table.DataEntry').on('click', 'button.DelButton', function() {
						const row = $(this).parents('tr');
						const that = this // here a is change
			            var output = true;
		                Swal.fire({
		                	title: "Are you sure?",
							text: "You won't be able to revert this!",
							icon: "warning",
							allowOutsideClick: false,
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {

		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		output = false;
		                        return false;
		                	}
		                	else if (result.value) {
								row.remove();		
		                	}
		                });
			        });

					////// Focus On Select All 
				    $("input[type='text']").click(function () {
					   $(this).select();
					});

				    ////// Existing Row Remove Confirmation
				    $('.DataEntry').on('change', '.DeleteCheckbox', function() {
						const row = $(this).closest('tr');
						const that = this // here a is change
			            if ($(this).is(":checked")){
			                var output = true;
			                 Swal.fire({
			                	title: "Are you sure?",
								text: "You won't be able to revert this!",
								icon: "warning",
								allowOutsideClick: false,
								showCancelButton: true,
								confirmButtonColor: "#3085d6",
								cancelButtonColor: "#d33",
								confirmButtonText: "Yes, Delete it...!",
								cancelButtonText: "Cancel",
								buttonsStyling: true
			                }).then(function(result) {

			                	if (result.dismiss === Swal.DismissReason.cancel) {
			                		that.checked= false;// here is a change
			                        output = false;
			                	}
			                	else if (result.value) {

			                		$.ajax({
						                url: '<?php echo base_url($this->_clspath) ?>job_document/detach',
						                type: 'post',
						                dataType: 'json',
						                data: {
						                	doc_id: row.find('.DocId').val(), 
						                	job_id: id,
						                },
						                beforeSend: function(){
								            $("#loader").show();
								        },
						                success:function(response) {
						                	if(response.success == true){
						                		row.remove();
						                		$("#loader").hide();
						                		Swal.fire({
											        title: 'Wow...!',
											        html:  ""+response.messages+"",
											        icon: "success",
											    });

						                	}
						                	else
						                	{
						                		$("#loader").hide();
						                		Swal.fire({
											        title: 'Opps...!',
											        html:  ""+response.messages+"",
											        icon: "error",
											    });
						                	}
						                }
						            });

			                	}
			                });
			            }
			        });

                   


                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
            $("#createGlobalModel").modal('hide');
            $("#loader").hide();
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

    function closeDocumentModel(){
    	$('#modal-import-document').modal('hide');
    	$('#modal-import-document').find('#documentUpload').html('');
    }

    function updateContainers_old(id = null)
    {   
    	if(! id){
    		Swal.fire({
		        title: 'Opps...!',
		        html:  "No Container Available in Job",
		        icon: "warning",
		    });
		    return false;
        }
        
        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>import/container/index/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.rows.length == 0){
                		$("#loader").hide();
                		Swal.fire({
					        title: 'Opps...!',
					        html:  "No Container Available in Job",
					        icon: "warning",
					    });
					    return false;
                	}
                	
                    var my_html = '';    
                    <?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
						my_html += '<form action="<?php echo base_url('import/container/edit/') ?>'+id+'" method="post" id="frmUpdateContainers">';
							my_html += '<div class="modal-body p-0">';
								my_html += '<div class="row col-md-12 m-1">';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Container Booking No</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="booking_no" value="'+response.jobs.cntr_booking_no+'" id="BookingNo">';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Booking Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="booking_date" value="'+response.jobs.cntr_booking_date+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Vessel name / Voyage</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="vessel_name" value="'+response.jobs.vessel_name+'" id="VesselName">';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';

								my_html += '<div class="row col-md-12 m-1">';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">ETA Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="fpod_eta" value="'+response.jobs.fpod_eta+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">ETD Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="fpod_etd" value="'+response.jobs.fpod_etd+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-4">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Expiry Date</label>';
											my_html += '<div class="input-group input-group-sm">';
												my_html += '<input type="text" class="form-control DatePicker" name="expiry_date" value="'+response.jobs.expiry_date+'">';
												my_html += '<div class="input-group-append">';
													my_html += '<div class="input-group-text"><i class="icon-calendar"></i></div>';
												my_html += '</div>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';

								my_html += '<div class="row col-md-12 m-1">';
									my_html += '<div class="col-md-3">';
										my_html += '<div class="form-group">';
											my_html += '<label class="control-label">Booking Copy</label>';
											my_html += '<input type="file" name="">';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';

								my_html += '<div class="table-responsive modalcustome">';
							        my_html += '<table class="table table-sm nowrap">';
							            my_html += '<thead>';
							                my_html += '<tr>';
							                    my_html += '<th class="text-center align-middle">##</th>';
							                    my_html += '<th class="align-middle">Container No</th>';
							                    my_html += '<th class="align-middle">Size</th>';
							                    my_html += '<th class="align-middle">Container Type</th>';
							                    my_html += '<th class="align-middle">From (Pickup)</th>';
							                    my_html += '<th class="align-middle">To (Delivery)</th>';
							                    my_html += '<th class="align-middle">Vehicle No</th>';
							                    my_html += '<th class="align-middle">Transporter Name</th>';
							                    my_html += '<th class="align-middle">Line Seal</th>';
							                    my_html += '<th class="align-middle">Shipper Seal</th>';
							                    my_html += '<th class="align-middle">Custom Seal</th>';
							                    my_html += '<th class="align-middle d-none">Rcvd. Nett Weight</th>';
							                    my_html += '<th class="text-center align-middle width-80"><a href="#" class="CheckAll"><i class="fa fa-trash-alt"></i></a></th>';

							                my_html += '</tr>';
							            my_html += '</thead>';
							            my_html += '<tbody>';

							           my_html += '<tr class"TemplateRow">';
											my_html += '<td class="align-middle text-center"><b></b></td>';
											my_html += '<td class="text-center align-middle">';
												my_html += '<input type="hidden" name="id[]" value="">';
												my_html += '<input type="text" class="form-control form-control-sm" name="number[]" id="new_number[]" value="">';
											my_html += '</td>';
											my_html += '<td class="align-middle text-danger text-center"><b></b></td>';
										    my_html += '<td class="align-middle"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_from[]"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_to[]" ></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_vehicle_no[]"></td>';
										    my_html += '<td class="align-middle"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_line_seal[]" onkeypress="return isNumber(event)"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_shipper_seal[]" onkeypress="return isNumber(event)"></td>';
										    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="new_custom_seal[]" onkeypress="return isNumber(event)"></td>';
										    my_html += '<td class="align-middle d-none"><input type="text" class="form-control form-control-sm" onkeypress="return isNumberDot(event)" name="new_net_weight[]"></td>';
										    my_html += '<td class="align-middle text-center"><button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button></td>';
										    
										my_html += '</tr>';

										if(response.rows.length){
									        var result = JSON.parse(JSON.stringify(response.rows));
									        var $i;

									        for(var i=0;i < result.length;i++){
									            var item = result[i]; 

									            // var editBtn = '<a href="javascript: updateContainer('+id+','+item.id+')" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></a>';
									            // my_html += '<tr><td class="text-center align-middle">'+editBtn+'</td>';
									            // 	my_html += '<td class="align-middle">'+item.number+'</td>';
									            // 	my_html += '<td class="align-middle text-danger"><b>'+item.size+'</b></td>';
									            //     my_html += '<td class="align-middle">'+item.container_type+'</td>';
									            //     my_html += '<td class="align-middle">'+item.from+'</td>';
									            //     my_html += '<td class="align-middle">'+item.to+'</td>';
									            //     my_html += '<td class="align-middle">'+item.vehicle_no+'</td>';
									            //     my_html += '<td class="align-middle">'+item.transporter+'</td>';
									            //     my_html += '<td class="align-middle">'+item.line_seal+'</td>';
									            //     my_html += '<td class="align-middle">'+item.shipper_seal+'</td>';
									            //     my_html += '<td class="align-middle">'+item.custom_seal+'</td>';
									            //     my_html += '<td class="align-middle text-right">'+item.net_weight+'</td>';
									            // my_html += '</tr>';

												

									        }
									    }
						                else
						                {
						                    my_html += '<tr><td colspan="12" class="align-middle"><div class="alert alert-danger text-center">No Documents Records Found.</div></td></tr>';
						                }

						                

							            my_html += '</tbody>'; 
							        my_html += '</table>';
							    my_html += '</div>';
						    my_html += '</div>';
							my_html += '<div class="modal-footer">';
							my_html += '<button type="submit" class="btn btn-success AddButton mr-auto"><i class="icon-white icon-plus"></i> New Item</button>';
								my_html += '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
								my_html += '<button type="submit" class="btn btn-success text-left"><i class="fa fa-save"></i> Update</button>';
							my_html += '</div>';
						my_html += '</form>';

					<?php //} ?>        
                   
					if(my_html)
                        $('#updateContainers').append(my_html);
                    
                    $("#modal-containers").modal('show');
                    $("#loader").hide();

                    function cloneRow(template_row) {

                    	$new_row = $(template_row).clone();
						$new_row.children('td:last').html(del_button);
						$new_row.removeClass('TemplateRow');
						//$new_row.removeClass('d-none');
						//$new_row.addClass('lastRowElement');
						$new_row.insertBefore($(template_row));

						$(template_row).find('textarea').each(function(index, el) {
							$new_row.find('textarea').eq(index).val($(this).val());
						});
						
						$(template_row).find('select').each(function(index, el) {
							$new_row.find('select').eq(index).val($(this).val());
						});
						$(template_row).find(':input').not('.Unchanged').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', '');
							else
								$(this).val('');
							
						});

						$(template_row).find('input[type="checkbox"]').not('.Unchanged').prop('checked', '');

						$(template_row).find('td.ClearText').each(function() {
							$(this).text('');
						});
						$(template_row).find('.Increment').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', (parseInt($(this).val(), 10)+1));
							else
								$(this).val(parseInt($(this).val(), 10)+1);
						});
						$(template_row).find('.DataDefault').each(function() {
							$(this).val($(this).attr('data-default'));
						});

						$(template_row).find(':input.Focus').focus();
					}

					////// CLONE NEW ROW
					$('#modal-containers').unbind().on('click', 'button.AddButton', function(e) {

						var submit_row = $(this).hasClass('RowSubmit');
						var clone_row  = true;
						$template_row  = $(this).parents('#updateContainers').find('table.DataEntry').find('tr.TemplateRow');
						//$validate_row  = $(this).parents('#updateContainers').find('table.DataEntry').find('tr.lastRowElement');

						// $validate_row.find('.Validate').each(function(index, el) {
						// 	if ($(this).val() === '') {
						// 		//console.log($(this).attr('class'));
						// 		clone_row = false;
						// 		$(this).addClass('is-invalid');
						// 	}
						// 	else {
						// 		$(this).removeClass('is-invalid');
						// 	}
						// });

						if (clone_row) {
							
							cloneRow($template_row);
				            if (submit_row === false) {
								e.preventDefault();
							}
						}
						else {
							e.preventDefault();
						}
					});

					// Data Entry +/-
					var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';

					////// Delete Attachment
					$('table.DataEntry').on('click', 'a.deleteAttach', function(event) {
						var job_id = id;
						var itemrow_id = $(this).parents('tr').find('.ItemRowID').val();
						Swal.fire({
		                	title: "Are you sure?",
							text: "You want to delete this attachement..!",
							icon: "warning",
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {
		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		event.preventDefault();
					       		event.stopPropagation();
		                	}
		                	else if (result.value) {
		                		$.ajax({
								  	url: '<?php echo site_url('import') ?>/container/deleteattach',
								  	type: 'POST',
								  	data: { job_id : job_id, row_id : itemrow_id },
								  	dataType: 'json',
								  	beforeSend: function(){
							            $("#loader").show();
							        },
								  	success:function(response) {
								  		if(response.success === true) {
								  			$("#loader").hide();
								  			Swal.fire({
										        title: 'Wow...!',
										        html:  ""+response.messages+"",
										        icon: "success",
										    }).then(function(result) {
										    	$('#modal-costsheet').find('#updateContainers').html('');
										    	updateCostsheet(job_id);
										    });
										} 
										else 
										{
											$("#loader").hide();
										  	Swal.fire({
										        title: 'Opps...!',
										        html:  ""+response.messages+"",
										        icon: "error",
										    });
										}
										return false;
									}
								}); 
		                	}
		                });
		                event.preventDefault();
					    event.stopPropagation();

					});

					////// New Added Row Remove Confirmation
					$('table.DataEntry').on('click', 'button.DelButton', function() {
						const row = $(this).parents('tr');
						const that = this // here a is change
			            var output = true;
		                Swal.fire({
		                	title: "Are you sure?",
							text: "You won't be able to revert this!",
							icon: "warning",
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {

		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		output = false;
		                        return false;
		                	}
		                	else if (result.value) {
								row.remove();		
		                	}
		                });
			        });

					////// Focus On Select All 
				    $("input[type='text']").click(function () {
					   $(this).select();
					});

				    ////// Existing Row Remove Confirmation
				    $('.DataEntry').on('change', '.DeleteCheckbox', function() {
						const row = $(this).closest('tr');
						const that = this // here a is change
			            if ($(this).is(":checked")){
			                var output = true;
			                 Swal.fire({
			                	title: "Are you sure?",
								text: "You won't be able to revert this!",
								icon: "warning",
								showCancelButton: true,
								confirmButtonColor: "#3085d6",
								cancelButtonColor: "#d33",
								confirmButtonText: "Yes, Delete it...!",
								cancelButtonText: "Cancel",
								buttonsStyling: true
			                }).then(function(result) {

			                	if (result.dismiss === Swal.DismissReason.cancel) {
			                		that.checked= false;// here is a change
			                        output = false;
			                	}
			                	else if (result.value) {
									if (!that.checked)
							            row.fadeIn('slow');
							        else 
							            row.fadeOut('slow');	
			                	}
			                });
			            }
			        });

                    /////////////////////////////////////////////////////////////////////////////////////

                    $('.select2').select2({
				      theme: 'bootstrap4'
				    })

				    $('.select2-selection__rendered').hover(function () {
					    $(this).removeAttr('title');
					});

					// on first focus (bubbles up to document), open the menu
					$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
					  $(this).closest(".select2-container").siblings('select:enabled').select2('open');
					});

					// steal focus during close - only capture once and stop propogation
					$('select.select2').on('select2:closing', function (e) {
					  $(e.target).data("select2").$selection.one('focus focusin', function (e) {
					    e.stopPropagation();
					  });
					});

					$(document).on('select2:open', () => {
					    document.querySelector('.select2-search__field').focus();
					});

					/////////////////////////////////////////////////////////////////////////////////////

                    $("#frmUpdateContainers").unbind('submit').on('submit', function() {
						var form = $(this);

						// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: form.attr('action'),
						  	type: form.attr('method'),
						  	data: form.serialize(), // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	beforeSend: function(){
					            $("#loader").show();
					        },
						  	success:function(response) {
						  		if(response.success === true) {
						  			$("#loader").hide();
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    }).then(function() {
								    	$("#frmUpdateContainers")[0].reset();
								  		$("#frmUpdateContainers .form-control").removeClass('is-invalid').removeClass('is-valid');
								  		//$("#modal-containers").modal('hide');
								  		$('#updateContainers').html('');
										updateContainers(id);
									    //$("#modal-containers").modal('show');
								    });
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								    $("#loader").hide();
								  } 
								  else 
								  {
								  	$("#loader").hide();
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
							}
						}); 
						return false;
					});

                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
        	$("#loader").hide();
            $("#modal-containers").modal('hide');
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

    function updateContainer(job_id = null, id = null)
    {   

    	if(job_id & id){

    		$.ajax({
                url: '<?php echo site_url() ?>import/container/edit/'+job_id+'/'+id,
                type: 'get',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.success == true){
                		
                		var my_html = '';    
                		<?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
							my_html += '<form action="<?php echo base_url('import/container/edit/') ?>'+job_id+'/'+id+'" method="post" id="frmUpdateContainer">';
								my_html += '<div class="modal-body">';
									my_html += '<div class="row">';
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Container No</label>';
												my_html += '<input type="hidden" name="id" value="'+id+'">';
												my_html += '<input type="hidden" name="job_id" value="'+job_id+'">';
												my_html += '<input type="text" class="form-control form-control-sm" name="number" id="number" value="'+response.row.number+'">';
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Type</label>';
												my_html += response.container_types;
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">From (Pickup Location)</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="from" id="from" value="'+response.row.from+'">';
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">To</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="to" id="to" value="'+response.row.to+'">';
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Vehicle No</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="vehicle_no" id="vehicle_no" value="'+response.row.vehicle_no+'">';
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Transporter</label>';
												my_html += response.transporter;
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-4">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Line Seal</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="line_seal" id="line_seal" value="'+response.row.line_seal+'">';
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-4">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Shipper Seal</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="shipper_seal" id="shipper_seal" value="'+response.row.shipper_seal+'">';
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-4">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Custome Seal</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="custom_seal" id="custom_seal" value="'+response.row.custom_seal+'">';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';

								my_html += '<div class="modal-footer">';
									my_html += '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
									my_html += '<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>';
								my_html += '</div>';
							my_html += '</form>';

						<?php //} ?>   

						$("#loader").hide();
                	}
                	else
	                {
	                	$("#loader").hide();
	                	Swal.fire({
                            title: 'Opps...!!!',
                            html:  ""+response.messages+"",
                            icon: "error",
                        });
	                }
                	
	                if(my_html)
                        $('#updateContainer').append(my_html);
                    
                    $("#modal-containers").modal('hide');
                    $("#modal-container").modal('toggle');

					$("#frmUpdateContainer").unbind('submit').on('submit', function() {
						var form = $(this);

						// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: form.attr('action'),
						  	type: form.attr('method'),
						  	data: form.serialize(), // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	beforeSend: function(){
					            $("#loader").show();
					        },
						  	success:function(response) {
						  		if(response.success === true) {

									$("#modal-container").modal('hide');
									updateContainers(job_id);
								    //$("#modal-containers").modal('show');
								    $("#loader").hide();
									Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    }).then(function() {
								    	$("#frmUpdateContainer")[0].reset();
								  		$("#frmUpdateContainer .form-control").removeClass('is-invalid').removeClass('is-valid');
								    });
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								    $("#loader").hide();
								  } 
								  else 
								  {
								  	$("#loader").hide();
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
							}
						}); 
						return false;
					});



                },
                error: function (result) {
                    $("#loader").hide();
                    Swal.fire({
				        title: '(: Opps :)',
				        html:  "Something went wrong...!",
				        icon: "error",
				    }).then(function() {
				    	return false
				    });
                },
            });
        }
        else
        {
            Swal.fire({
		        title: '(: Opps :)',
		        html:  "Something went wrong...!",
		        icon: "error",
		    }).then(function() {
		    	return false
		    });
        }
    }

    function updateMaster(type = null, category = null)
    {   

    	var my_html = '';    
		<?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
			my_html += '<form action="<?php echo base_url('master/') ?>'+type+'/ajaxEdit/0/'+category+'" method="post" id="frmUpdateMaster">';
				my_html += '<div class="modal-body">';
					
						
					my_html += '<div class="form-group">';
						my_html += '<label class="control-label">Enter '+category+' Name</label>';
						my_html += '<input type="text" class="form-control form-control-sm" name="name" id="name" value="">';
					my_html += '</div>';
						
					
				my_html += '</div>';
				my_html += '<div class="modal-footer">';
					my_html += '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
					my_html += '<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>';
				my_html += '</div>';
			my_html += '</form>';

		<?php //} ?>   

            	
        if(my_html)
            $('#masterUpdate').append(my_html);
        
        $("#modal-master-update").modal('show');

		$("#frmUpdateMaster").unbind('submit').on('submit', function() {
			
			var form = $(this);
			// remove the text-danger
			$(".text-danger").remove();

			$.ajax({
			  	url: form.attr('action'),
			  	type: form.attr('method'),
			  	data: form.serialize(), // /converting the form data into array and sending it to server
			  	dataType: 'json',
			  	beforeSend: function(){
		            $("#loader").show();
		        },
			  	success:function(response) {

			  		if(response.success === true) {
			  			
						$("#modal-master-update").modal('hide');

						if(category == 'Party'){
							$("#ajaxParty").val(response.name);
							$("#PartyID").val(response.id);
						}
						else if(category == 'Importer'){
							$("#ajaxShipper").val(response.name);
							$("#ShipperID").val(response.id);
						}
						else if(category == 'Consignee'){
							$("#ajaxConsignee").val(response.name);
							$("#ConsigneeID").val(response.id);
						}
						else if(category == 'Buyer'){
							$("#ajaxBuyer").val(response.name);
							$("#BuyerID").val(response.id);
						}
						else if(category == 'Notify'){
							$("#ajaxNotify1Name").val(response.name);
							$("#notify1ID").val(response.id);
						}
						else if(category == 'Forwarder'){
							$("#ajaxforwarder").val(response.name);
							$("#forwarderID").val(response.id);
						}
						else if(category == 'Line'){
							$("#ajaxLine").val(response.name);
							$("#LineID").val(response.id);
						}
						else if(category == 'CHA'){
							$("#ajaxCHA").val(response.name);
							$("#CHAID").val(response.id);
						}
						else if(category == 'CFS'){
							$("#ajaxCFS").val(response.name);
							$("#CFSID").val(response.id);
						}
					    $("#loader").hide();
					    Swal.fire({
					        title: 'Wow...!',
					        html:  ""+response.messages+"",
					        icon: "success",
					    }).then(function() {
					    	$("#frmUpdateMaster")[0].reset();
					  		$("#frmUpdateMaster .form-control").removeClass('is-invalid').removeClass('is-valid');
					    });
					} 
					else 
					{

					  if(response.messages instanceof Object) {
					    $.each(response.messages, function(index, value) {
					      var id = $("#"+index);

					      id.removeClass('is-invalid')
					      .removeClass('is-valid')
					      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
					      
					      id.after(value);
					    });
					    $("#loader").hide();
					  } 
					  else 
					  {
					  	$("#loader").hide();
					  	Swal.fire({
					        title: 'Opps...!',
					        html:  ""+response.messages+"",
					        icon: "error",
					    });
					  }
					}
				}
			}); 
			return false;
		});




    }

    function updatePort(type = null, category = null)
    {   

    	var my_html = '';    
		<?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
			my_html += '<form action="<?php echo base_url('master/') ?>'+type+'/ajaxEdit/0/'+category+'" method="post" id="frmUpdatePort">';
				my_html += '<div class="modal-body">';
					
						
					my_html += '<div class="form-group">';
						my_html += '<label class="control-label">Enter Code</label>';
						my_html += '<input type="text" class="form-control form-control-sm" name="code" id="code" value="">';
					my_html += '</div>';
					my_html += '<div class="form-group">';
						my_html += '<label class="control-label">Enter Unece Code</label>';
						my_html += '<input type="text" class="form-control form-control-sm" name="unece_code" id="unece_code" value="">';
					my_html += '</div>';
					my_html += '<div class="form-group">';
						my_html += '<label class="control-label">Enter Name</label>';
						my_html += '<input type="text" class="form-control form-control-sm" name="name" id="name" value="">';
					my_html += '</div>';
					my_html += '<div class="form-group">';
						my_html += '<label class="control-label">Enter Name</label>';
						my_html += '<input type="text" class="form-control form-control-sm" name="country_id" id="country_id" value="">';
						my_html += '<input type="text" class="form-control form-control-sm" name="country_name" id="country_name" value="">';
					my_html += '</div>';
						
					
				my_html += '</div>';
				my_html += '<div class="modal-footer">';
					my_html += '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
					my_html += '<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>';
				my_html += '</div>';
			my_html += '</form>';

		<?php //} ?>   

            	
        if(my_html)
            $('#portUpdate').append(my_html);
        
        $("#modal-port-update").modal('show');

		$("#frmUpdatePort").unbind('submit').on('submit', function() {
			
			var form = $(this);
			// remove the text-danger
			$(".text-danger").remove();

			$.ajax({
			  	url: form.attr('action'),
			  	type: form.attr('method'),
			  	data: form.serialize(), // /converting the form data into array and sending it to server
			  	dataType: 'json',
			  	beforeSend: function(){
		            $("#loader").show();
		        },
			  	success:function(response) {
			  		if(response.success === true) {
			  			$("#loader").hide();
						$("#modal-port-update").modal('hide');

						if(category == 'Indian'){
							$("#ajaxCustomPort").val(response.name);
							$("#CustomPortID").val(response.id);
							$("#ajaxDestinationPort").val(response.name);
							$("#DestinationPortID").val(response.id);
						}
						else if(category == 'Global'){
							$("#ajaxDischargePort").val(response.name);
							$("#DischargePortID").val(response.id);
							$("#ajaxLoadingPort").val(response.name);
							$("#LoadingPortID").val(response.id);
						}
					    $("#loader").hide();
					    Swal.fire({
					        title: 'Wow...!',
					        html:  ""+response.messages+"",
					        icon: "success",
					    }).then(function() {
					    	$("#frmUpdateMaster")[0].reset();
					  		$("#frmUpdateMaster .form-control").removeClass('is-invalid').removeClass('is-valid');
					    });
					} 
					else 
					{

					  if(response.messages instanceof Object) {
					    $.each(response.messages, function(index, value) {
					      var id = $("#"+index);

					      id.removeClass('is-invalid')
					      .removeClass('is-valid')
					      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
					      
					      id.after(value);
					    });
					    $("#loader").hide();
					  } 
					  else 
					  {
					  	$("#loader").hide();
					  	Swal.fire({
					        title: 'Opps...!',
					        html:  ""+response.messages+"",
					        icon: "error",
					    });
					  }
					}
				}
			}); 
			return false;
		});
    }
</script>