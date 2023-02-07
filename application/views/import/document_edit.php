<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/deleteJob/'.$job_id.'/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo base_url('vendor/tinymce/tinymce/tinymce.min.js') ?>"></script>
<script type="text/javascript">
tinymce.init({
	// General options
	selector : "textarea",
	theme : "modern",
	plugins : [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
		"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		"save table contextmenu directionality emoticons template paste textcolor",
	],
	toolbar : [
		"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,cleanup,help,code,|,forecolor,backcolor",
		"fontselect,fontsizeselect,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,fullscreen,pagebreak,|,tablecontrols,|,hr,removeformat,|,template",
	],
	content_css : ["/assets/css/print.css"],

	// Drop lists for link/image/media/template dialogs
	templates : "<?php echo base_url('master/document_template/getTemplateListJS') ?>",
	template_replace_values    : {
		today_date 		: "<?php echo date('d-m-Y') ?>",

		company_name 	: "<?php echo $company['name'] ?>",
		company_address : "<?php echo $company['address'] ?>",
		company_city 	: "<?php echo $company['city'] ?>",
		company_contact : "<?php echo $company['contact'] ?>",
		company_pan_no  : "<?php echo $company['pan_no'] ?>",
		company_stax_no : "<?php echo $company['service_tax_no'] ?>",

		date 		: "<?php echo $job['date'] ?>",
		importer    : "<?php echo $party['name'] ?>",
		hss_buyer   : "<?php echo $hss_buyer['name'] ?>",
		party_name  : "<?php echo (isset($hss_buyer) ? $hss_buyer['name'] : $party['name']) ?>",
		party_address : "<?php echo (isset($hss_buyer) ? $hss_buyer['address'] : $party['address']) ?>",
		vessel_name : "<?php echo $vessel_name ?>",
		bl_no 		: "<?php echo $job['bl_no'] ?>",
		bl_date 	: "<?php echo $job['bl_date'] ?>",
		be_no 		: "<?php echo $job['be_no'] ?>",
		be_date 	: "<?php echo $job['be_date'] ?>",

		type 		: "<?php echo $job['type'] ?>",
		cargo_type 	: "<?php echo $job['cargo_type'] ?>",
		product_name: "<?php echo $product_name ?>",
		pieces 		: "<?php echo $job['packages'] ?>",
		pieces_unit : "<?php echo $package_type ?>",
		cbm 		: "<?php echo $job['net_weight'] ?>",
		cbm_unit	: "<?php echo $job['net_weight_unit'] ?>",
		gross_weight: "<?php echo $job['gross_weight'] ?>",
		gross_weight_unit: "<?php echo $job['gross_weight_unit'] ?>",

		containers	: "<?php echo ($job['container_20'] > 0 ? '(' . $job['container_20'] . 'x20) ' : NULL) . ($job['container_40'] > 0 ? '(' . $job['container_40'] . 'x40) ' : NULL) ?>",

		invoice_no  : "<?php echo $job['invoice_no'] ?>",
		invoice_date: "<?php echo $job['invoice_date'] ?>"
,		description : "<?php echo $job['details'] ?>",
		marks		: "<?php echo str_replace("\r", '', str_replace("\n", '', $job['marks'])) ?>",

		indian_port 	: "<?php echo $indian_port ?>",
		shipment_port 	: "<?php echo $shipment_port ?>",
		origin_country 	: "<?php echo $origin_country ?>",
		cha_name        : "<?php echo $cha_name ?>",
		shipper_name    : "<?php echo $shipper_name ?>",
		line_name       : "<?php echo $line_name ?>",
	}
});
</script>
<!-- /TinyMCE -->

<?php
echo form_open($this->uri->uri_string());
echo form_hidden($id);
?>

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
		<fieldset>
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Document No</label>
						<input type="text" class="form-control form-control-sm" name="document_no" value="<?php echo $row['document_no'] ?>" id="Document" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Date</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo $row['date']; ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-8">
					<div class="form-group">
						<label class="control-label">Document Name</label>
						<input type="text" class="form-control form-control-sm<?php echo (strlen(form_error('name')) > 0 ? ' error' : '') ?>" name="name" value="<?php echo $row['name'] ?>" id="Name" />
					</div>
				</div>
			</div>
			<br />

			<input type="hidden" name="document" id="Document" value="" />
			<textarea type="text" class="form-control form-control-sm" rows="20" cols="80" id="DocumentEditor"><?php echo $row['document'] ?></textarea>
		</fieldset>		
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<?php if ($id['id'] > 0) {
		echo '<a href="#modal-delete" class="btn btn-danger" data-toggle="modal">Delete</a>&nbsp;&nbsp;
			<div class="btn-group">' . 
			anchor($this->_clspath.$this->_class.'/preview/'.$id['id'].'/0', 'HTML', 'class="btn btn-default Popup"') . 
			anchor($this->_clspath.$this->_class.'/preview/'.$id['id'], 'PDF', 'class="btn btn-default Popup"') .  
	    	'</div>';
	    } ?>
	</div>
</div>
</form>

<script>
$('form').submit(function(e) {
	e.preventDefault();
	var content = tinymce.get('DocumentEditor').getContent();
	$("#Document").val($.base64.encode(content));
	this.submit();
});

$(document).ready(function() {
	$("#Name").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/documents/name') ?>',
		minLength: 1
	});
});
</script>
