<!-- TinyMCE -->
<script type="text/javascript" src="/vendor/tinymce/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
	// General options
	selector : ".TemplateEditor",
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
});
</script>
<!-- /TinyMCE -->

<div id="modal-logo" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/addLogo/'.$id['id'], array('id' => 'LogoForm')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Upload Logo</h3>
			</div>
			<div class="modal-body">
				<input type="file" name="userfile" size="40" />
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-logo-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/delLogo/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
  			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select Branch : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('branchList', [], '', 'class="form-control mb-0" id="branchSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="card-body">
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Parent Company</label>
					<?php echo form_dropdown('company_id', getSelectOptions('companies'), '', 'class="form-control form-control-sm Selectize" id="company_id"'); ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Branch Series</label>
					<input type="text" class="form-control form-control-sm" name="series" value="" id="series" />
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Branch Code</label>
					<input type="text" class="form-control form-control-sm" name="code" value="" id="code"/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Branch Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="" id="name"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<div class="form-group">
					<label class="control-label">Address</label>
					<input type="text" class="form-control form-control-sm" name="address" id="address" />
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">City</label>
					<input type="text" class="form-control form-control-sm" name="city_id" id="ajaxCity" value="" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">Contact</label>
					<input type="text" class="form-control form-control-sm" name="contact" value="" id="contact" />
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">Email</label>
					<input type="text" class="form-control form-control-sm" name="email" value="" id="email"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<input type="text" class="form-control form-control-sm" name="pan_no" value="" id="pan_no" />
				</div>
					<label class="control-label">Pan No</label>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Tan No</label>
					<input type="text" class="form-control form-control-sm" name="tan_no" value="" id="tan_no"/>
				</div>
			</div>
		
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Service Tax No</label>
					<input type="text" class="form-control form-control-sm" name="service_tax_no" value="" id="service_tax_no"/>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">GST No</label>
					<input type="text" class="form-control form-control-sm" name="gst_no" value="" id="gst_no" />
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">CHA No</label>
					<input type="text" class="form-control form-control-sm" name="cha_no" value="" id="cha_no"/>
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">CHA License No</label>
					<input type="text" class="form-control form-control-sm" name="cha_license_no" value="" id="cha_license_no"/>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label">Remarks</label>
			<textarea class="form-control form-control-sm" name="remarks" rows="2" id="remarks"></textarea>
		</div>
		

	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-success" id="Add" onclick="updateBranch('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Clear</button>
				</div>
			</div>
		</div>
	</div>	
</div>
</form>

<script>
	$(document).ready(function() {
		$('#ajaxCity').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/branches/id/city_id') ?>'});
	});
	
</script>

<script type="text/javascript">
	$(function() {
	    updateBranch('0');
	});
	$(document).ready(function() {

		$('#branchSelect2').select2({
				ajax: { 
					url: '<?php echo site_url($this->_clspath.$this->_class) ?>/branchList/',
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

		    	//console.log($(this).val())
		    	var branch_id = $(this).val();
		    	// $('#updateParty').html();
		    	updateBranch(branch_id);

		    });
		});

	function updateBranch(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getBranch/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.success == false){
                		$("#loader").hide();
                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
   						
					}
                	else
                	{
                		$('#MainForm')[0].reset();
	                	
	                	$("input[name=id]").val(response.row.id);
	            		$("#company_id").val(response.row.company_id);
	            		$("#series").val(response.row.series);
	            		$("#code").val(response.row.code);
	            		$("#name").val(response.row.name);
	            		$("#address").val(response.row.address);
	            		$("#ajaxCity").val(response.row.city_id);
	            		$("#contact").val(response.row.contact);
	            		$("#email").val(response.row.email);
	            		$("#pan_no").val(response.row.pan_no);
	            		$("#tan_no").val(response.row.tan_no);
	            		$("#service_tax_no").val(response.row.service_tax_no);
	            		$("#gst_no").val(response.row.gst_no);
	            		$("#cha_no").val(response.row.cha_no);
	            		$("#cha_license_no").val(response.row.cha_license_no);
	            		$("#remarks").val(response.row.remarks);

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
            	}
            });
        }
    }

	$('body').tooltip('dispose');


</script>
