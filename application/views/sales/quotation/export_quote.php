<?php
	echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"');
	echo form_hidden($id);
?>
<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title.' - '.$type)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select Qutation : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('quoteList', [], '', 'class="form-control mb-0" id="quoteSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="card-body">

			<div class="card card-default">
				<div class="card-header text-center">
					<h3 class="bold mb-0">Quotaion for <?php echo $type ?> + Transport (Custom clearance)</h3>
				</div>
				<div class="card-body p-0">
					<input type="hidden" class="form-control form-control-sm" name="type" value="">
					<div class="row m-0">
						<div class="col-lg-1 border-right border-right border-bottom">
						<span class="small bold">To : </span>
						</div>
						<div class="col-lg-5 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="quote_to" value="" id="To">
						</div>
						<div class="col-lg-2 border-right border-right border-bottom">
							<span class="small bold">Reference Num : </span>
						</div>
						<div class="col-lg-4 border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="ref_num" value="" id="RefNum">
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Add : </span>
						</div>
						<div class="col-lg-5 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="address" value="" id="Address">
							
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Date : </span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="date" value="<?php echo date('d-m-Y') ?>" id="Date">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Quote By : </span>
						</div>
						<div class="col-lg-5 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="quote_by" value="" id="QuoteBy">
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Phone : </span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="phone" value="" maxlength="10" onkeypress="return isNumber(event)" id="Phone">
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">PIC : </span>
						</div>
						<div class="col-lg-5 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="pic" value="" id="Pic">
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Quotation Validity</span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="quotation_validity" value="<?php echo date('d-m-Y') ?>" id="QuotaionValidity" />
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h6 class="bold p-2 mb-0"></h6> </div>
					</div>	
					<div class="row m-0">
						<div class="col-lg-2 border-right border-right border-bottom">
						<span class="small bold">POL</span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="pol" value="" id="Pol">
						</div>
						<div class="col-lg-2 border-right border-right border-bottom">
							<span class="small bold">Shipping Line</span>
						</div>
						<div class="col-lg-4 border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="shipping_line" value="" id="ShippingLine">
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">POD</span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="pod" value="" id="Pod">
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Transit Time</span>
						</div>
						<div class="col-lg-4 border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="transite_time" value="" id="TransitTime">
						</div>
					</div>

					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Final Destination</span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="final_destination" value="" id="FinalDestination">
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Vessel Schedule</span>
						</div>
						<div class="col-lg-4 border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="vessel_schedule" value="" id="VesselSchedule">
						</div>
					</div>

					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Type of Container</span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="toc" value="" id="Toc">
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Pick Up</span>
						</div>
						<div class="col-lg-4 border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="pick_up" value="" id="PickUp">
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Terms of Shipment</span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="tos" value="" id="Tos">
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Weight Unit</span>
						</div>
						<div class="col-lg-4 border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="weight_unit" value="" id="WeightUnit">
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Cargo Details</span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="cargo_details" value="" id="CargoDetails">
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Other (If Any)</span>
						</div>
						<div class="col-lg-4 border-bottom p-1">
							<input type="text" class="form-control form-control-sm" name="other" value="" id="other">
						</div>
					</div>
					<div id="ContainerList"></div>
					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h6 class="bold p-2 mb-0"></h1> </div>
					</div>
					<div class="row m-0">
						<table class="table table-condensed table-striped DataEntry">
							<thead>
								<tr>
									<th width="12%">Sr. No</th>
									<th width="40%">Charges Description</th>
									<th width="12%">Currency</th>
									<th width="15%">Amount</th>
									<th width="12%">Base On</th>
									<th width="12%">Taxable</th>
									<th width="25%" class="aligncenter"><a class="CheckAll"><i class="icon-trashcan"></i></a></th>
								</tr>
							</thead>

							<tbody>
								<?php
								
								foreach($quo_details as $index => $r) {
									echo '<tr>
										<td><input type="text" class="form-control form-control-sm text-center" name="sr_no[' . $r['id'] . ']" value="' . $index + 1 . '" /></td>
										<td><input type="text" class="form-control form-control-sm" name="charges_description[' . $r['id'] . ']" value="' . $r['charges_description'] . '" /></td>
										<td><input type="text" class="form-control form-control-sm" name="currency[' . $r['id'] . ']" value="' . $r['currency'] . '" /></td>
										<td><input type="text" class="form-control form-control-sm" name="amount[' . $r['id'] . ']" value="' . $r['amount'] . '" onkeypress="return isNumber(event)" /></td>							
										<td><input type="text" class="form-control form-control-sm" name="base_on[' . $r['id'] . ']" value="' . $r['base_on'] . '" /></td>
										<td><input type="text" class="form-control form-control-sm" name="taxable[' . $r['id'] . ']" value="' . $r['taxable'] . '" /></td>
										<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
									</tr>';
								}
								?>

								<tr class="TemplateRow" id="QuoteDetail">
									<td><input type="text" class="form-control form-control-sm Focus" readonly name="new_sr_no[]" value="" /></td>
									<td><input type="text" class="form-control form-control-sm Focus" name="new_charges_description[]" value="" /></td>
									<td><input type="text" class="form-control form-control-sm Focus" name="new_currency[]" value="" /></td>
									<td><input type="text" class="form-control form-control-sm Focus" name="new_amount[]" value="" onkeypress="return isNumber(event)"/></td>
									<td><input type="text" class="form-control form-control-sm Focus" name="new_base_on[]" value="" size="10" /></td>
									<td><input type="text" class="form-control form-control-sm Focus" name="new_taxable[]" value="" /></td>
									<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></div></td>
								</tr>
								
							</tbody>
						</table>
					</div>
					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h6 class="bold p-2 mb-0"></h1> </div>
					</div>
					<div class="row m-0">
						
						<div class="col-lg-12 border-right border-bottom p-1">
							<textarea class="form-control form-control-sm Focus" name="tnc_1" id="Tnc1"><?php echo $tnc_1 ?></textarea>
						</div>
					</div>
					<div class="row m-0">
						
						<div class="col-lg-12 border-right border-bottom p-1">
							<textarea class="form-control form-control-sm Focus" name="tnc_2" id="Tnc2"><?php echo $tnc_2 ?></textarea>
						</div>
					</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom text-center">
							<span class="small bold">1</span>
						</div>
						<div class="col-lg-11 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm Focus" name="tnc_3" value="<?php echo $tnc_3 ?>" id="Tnc3" />
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom text-center">
							<span class="small bold">2</span>
						</div>
						<div class="col-lg-11 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm Focus" name="tnc_4" value="<?php echo $tnc_4 ?>" id="Tnc4" />
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom text-center">
							<span class="small bold">3</span>
						</div>
						<div class="col-lg-11 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm Focus" name="tnc_5" value="<?php echo $tnc_5 ?>" id="Tn5" />
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom text-center">
							<span class="small bold">4</span>
						</div>
						<div class="col-lg-11 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm Focus" name="tnc_6" value="<?php echo $tnc_6 ?>" id="Tnc6" />
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom text-center">
							<span class="small bold">5</span>
						</div>
						<div class="col-lg-11 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm Focus" name="tnc_7" value="<?php echo $tnc_7 ?>" id="Tnc7" />
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom text-center">
							<span class="small bold">6</span>
						</div>
						<div class="col-lg-11 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm Focus" name="tnc_8" value="<?php echo $tnc_8 ?>" id="Tnc8" />
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom text-center">
							<span class="small bold">7</span>
						</div>
						<div class="col-lg-11 border-right border-bottom p-1">
							<input type="text" class="form-control form-control-sm Focus" name="tnc_9" value="<?php echo $tnc_9 ?>" id="Tnc9" />
						</div>
					</div>
				</div>
			</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-warning" id="Print"><i class="fa fa-print"></i> Print</button>
					<button type="button" class="btn btn-success" id="Add" onclick="updateQuote('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Delete</button>
				</div>
			</div>
		</div>
	</div>
</div>
</form>

<script type="text/javascript" language="JavaScript">
	$(function() {
	    updateQuote('0');
	});

	$(document).ready(function() {
		$('#quoteSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/quoteList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				beforeSend: function(){
		            $("#loader").show();
		        },
				processResults: function (response) {
					$("#loader").hide();
					// $('#countNos').text('Total Count : '+response.count);	
			  		return {
			     		results: response.data
			  		};
				},
				cache: true
			},
			
	    }).on("change", function () {

	    	//console.log($(this).val())
	    	var quote_id = $(this).val();
	    	//console.log(quote_id);return false;
	    	// $('#updateParty').html();
	    	updateQuote(quote_id);

	    });
	});

	function updateQuote(id = null)
    {   

    	if(id & id!=0){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getQuote/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {
                	console.log(response);
                	if(response.success == false){
                		$("#loader").hide();
                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
                		document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});
   						
					}
                	else
                	{
                		$('#MainForm')[0].reset();
	                	document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});
	                	
	                	$("input[name=id]").val(response.row.id);
	            		$("#To").val(response.row.quote_to);
	            		$("#RefNum").val(response.row.ref_num);
	            		$("#Address").val(response.row.address);
	            		$("#Date").val(response.row.date);
	            		$("#QuoteBy").val(response.row.quote_by);
	            		$("#Phone").val(response.row.phone);
	            		$("#Pic").val(response.row.pic);
	            		$("#QuotaionValidity").val(response.row.quotation_validity);
	            		$("#Pol").val(response.row.pol);
	            		$("#ShippingLine").val(response.row.shipping_line);
	            		$("#Pod").val(response.row.pod);
	            		$("#TransitTime").val(response.row.transite_time);
	            		$("#FinalDestination").val(response.row.final_destination);
	            		$("#VesselSchedule").val(response.row.vessel_schedule);
	            		$("#Toc").val(response.row.toc);
	    				$("#PickUp").val(response.row.pick_up);
	            		$("#Tos").val(response.row.tos);
	            		$("#WeightUnit").val(response.row.weight_unit);
	            		$("#CargoDetails").val(response.row.cargo_details);
	            		$("#other").val(response.row.other);
	            		$("#Tnc1").val(response.row.tnc_1);
	            		$("#Tnc2").val(response.row.tnc_2);
	            		$("#Tnc3").val(response.row.tnc_3);
	            		$("#Tnc4").val(response.row.tnc_4);
	            		$("#Tnc5").val(response.row.tnc_5);
	            		$("#Tnc6").val(response.row.tnc_6);
	            		$("#Tnc7").val(response.row.tnc_7);
	            		$("#Tnc8").val(response.row.tnc_8);
	            		$("#Tnc9").val(response.row.tnc_9);
	    				
	            		var quo_details = '';
	            		var quo = '';
	     				if(response.quo_details.length){
					    	var result = JSON.parse(JSON.stringify(response.quo_details));

					    	for (var i = 0; i < result.length; i++) {
						        quo_details += '<tr>';
									var no = i + 1;
									quo_details += '<td><input type="text" class="form-control form-control-sm Focus" readonly name="sr_no['+result[i].id+']" value="'+no+'"></td>';
									quo_details += '<td><input type="text" class="form-control form-control-sm Focus" name="charges_description['+result[i].id+']" value="'+result[i].charges_description+'"></td>';
									quo_details += '<td><input type="text" class="form-control form-control-sm Focus" name="currency['+result[i].id+']" value="'+result[i].currency+'"></td>';
									quo_details += '<td><input type="text" class="form-control form-control-sm Focus" name="amount['+result[i].id+']" value="'+result[i].amount+'" onkeypress="return isNumber(event)"></td>';
									quo_details += '<td><input type="text" class="form-control form-control-sm Focus" name="base_on['+result[i].id+']" value="'+result[i].base_on+'" size="10"></td>';
									quo_details += '<td><input type="text" class="form-control form-control-sm Focus" name="taxable['+result[i].id+']" value="'+result[i].taxable+'" size="10"></td>';								
									quo_details += '<td><button type="button" class="btn btn-danger btn-sm DelButton1"><i class="icon-minus fa fa-minus"></i></button></td>';
								quo_details += '</tr>';
								
							}
							
						}
						quo += '<tr>';
							quo += '<td>1.</td>';
							quo += '<td colspan="6"><textarea class="form-control form-control-sm Focus" name="tnc_1" id="Tnc1">'+response.row.tnc_1+'</textarea></td>';
						quo += '</tr>';
						quo += '<tr>';
							quo += '<td>2.</td>';
							quo += '<td colspan="6"><textarea class="form-control form-control-sm Focus" name="tnc_2" id="Tnc2">'+response.row.tnc_2+'</textarea></td>';
						quo += '</tr>';
						$('#QuoteDetail:last-child').before(quo_details);	
						$('#QuoteDetail:last-child').after(quo);	

						$("#loader").hide();
				    	
				    }

				    $('table.DataEntry').on('click', 'button.DelButton1', function() {
						const row = $(this).parents('tr');
						const that = this // here a is change
			            var output = true;

			            var tblType = $(this).parents('tr').parents('table').attr('id');

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
		                	else
		                	{
		                		
		                		
		                		var rowId = row.find('input[type=hidden]').val();
		                		if(tblType === 'uploadTbl')
		                			var deleteurl = 'attachments/'+id+'/'+rowId;

	                			$.ajax({
					                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/delDetails/'+deleteurl,
					                type: 'post',
					                dataType: 'json',
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
			        });

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

									    updateQuote(id);
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

					$('#Print').on('click', function(e){
						e.preventDefault();
						var id = $("input[name=id]").val();
						if(id > 0){
							let a = document.createElement('a');
							a.target = '_blank';
							a.href = "<?php echo base_url($this->_clspath.$this->_class.'/print/'); ?>"+id;
							a.click();
						}else{
							Swal.fire({
						        html:  "Please select any invoice for print...!",
						        icon: "error",
						    });
						}
					});
            	}
            });
        }
    }
</script>