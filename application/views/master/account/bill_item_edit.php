
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
			<?php echo anchor($this->_clspath.$this->_class."/delete/".$id['id'], 'Delete', 'class="btn btn-danger"') ?>
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
				<label class="col-md-3 col-form-label col-form-label-sm">Select BillItem : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('billList', [], '', 'class="form-control mb-0" id="billSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="card-body">
		<label class="control-label h5 pt-1 text-center" >Total Count: <?php echo $count ?></label>
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Code</label>
					<input type="text" class="form-control form-control-sm" name="code" value="" size="10" id="code" />
				</div>
			</div>
			<div class="col-md-10">
				<div class="form-group">
					<label class="control-label">Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="" size="40" id="name" />
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					
					<?php echo form_dropdown('stax_category_id', array(0=>'')+getSelectOptions('stax_categories', 'id', 'name'), '', 'class="form-control form-control-sm d-none"'); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Type</label>
					<?php echo form_dropdown('type', getEnumSetOptions('ledgers', 'type'), 'Services', 'class="form-control form-control-sm" id="type"'); ?>
				</div>
			</div>
		
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">CGST</label>
					<input type="text" class="form-control form-control-sm" name="cgst" value="" id="cgst" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">SGST</label>
					<input type="text" class="form-control form-control-sm" name="sgst" value="" id="sgst" />
				</div>
			</div>
			
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">IGST</label>
					<input type="text" class="form-control form-control-sm" name="igst" value="" id="igst" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">SAC / HSN</label>
					<input type="text" class="form-control form-control-sm" name="sac_hsn" value="" id="sac_hsn" />
				</div>
			</div>
			<!-- <div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Billing Type</label>
					<?php //echo form_dropdown('billing_type', getEnumSetOptions('ledgers', 'billing_type'), '', 'class="form-control form-control-sm" id="billing_type"'); ?>
				</div>
			</div> -->
		</div>
		
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Reimbusment</label>
					<?php echo form_dropdown('reimbursement', getEnumSetOptions('ledgers', 'reimbursement'), '', 'class="form-control form-control-sm" id="reimbusment"'); ?>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Job Required</label>
					<?php echo form_dropdown('job_required', getEnumSetOptions('ledgers', 'job_required'), 'Yes', 'class="form-control form-control-sm" id="job_required"'); ?>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Active</label>
					<?php echo form_dropdown('active', getEnumSetOptions('ledgers', 'active'), '', 'class="form-control form-control-sm" id="active"'); ?>
				</div>
			</div>
		</div>
			
		<div class="form-group">
			<label class="control-label">Remarks</label>
			<textarea name="remarks" class="form-control form-control-sm" id="remarks"></textarea>
		</div>
	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-success" id="Add" onclick="updateBill('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Delete</button>
				</div>
			</div>
		</div>
	</div>	
</div>
</form>

<script type="text/javascript">
	$(document).ready(function() {

	$(function() {
	    updateBill('0');
	});

	$("#sac_hsn").kaabar_typeahead({
		name: 'tt_sac_hsn',
		displayKey: 'id',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/json/goods_services/id/name') ?>',
		suggestion: '<p><strong>{{id}}</strong> - {{name}}</p>'
	});

	$('#billSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/billList/',
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
	    	var bill_id = $(this).val();
	    	// $('#updateParty').html();
	    	updateBill(bill_id);

	    });
	});

	function updateBill(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getBill/'+id,
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
	            		$("#code").val(response.row.code);
	            		$("#name").val(response.row.name);
	            		$("#type").val(response.row.type);
	            		$("#cgst").val(response.row.cgst);
	            		$("#sgst").val(response.row.sgst);
	            		$("#igst").val(response.row.igst);
	            		$("#sac_hsn").val(response.row.sac_hsn);
	            		//$("#billing_type").val(response.row.billing_type);
	            		$("#reimbusment").val(response.row.reimbursement);
	            		$("#job_required").val(response.row.job_required);
	            		$("#active").val(response.row.active);
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
</script>