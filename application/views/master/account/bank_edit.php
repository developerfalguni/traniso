<?php
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select Bank : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('bankList', [], '', 'class="form-control mb-0" id="bankSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<label class="control-label h5 pt-1 text-center" >Total Count: <?php echo $count ?></label>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="control-label">Id</label>
					<input type="text" class="form-control form-control-sm" name="id" value="" id="id">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="control-label">Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="" id="name">
				</div>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-success" id="Add" onclick="updateBnak('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Clear</button>
				</div>
			</div>
		</div>
	</div>	
</div>
</form>

<script type="text/javascript" language="JavaScript">
	$(function() {
	    updateBnak('0');
	});
	$(document).ready(function() {
		$('#bankSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/bankList/',
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
	    	var bank_id = $(this).val();
	    	// $('#updateParty').html();
	    	updateBnak(bank_id);

	    });
	});

	function updateBnak(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getBank/'+id,
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
	            		$("#name").val(response.row.name);
	            		$("#id").val(response.row.id);

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