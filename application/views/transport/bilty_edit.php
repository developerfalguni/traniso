<?php
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select LR : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('lrList', [], '', 'class="form-control mb-0" id="lrSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control DatePicker" name="date" value="<?php echo date('d-m-Y') ?>" id="date" />
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">From</label>
					<input type="text" class="form-control form-control-sm" name="from_place" value="" id="from_place">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">To</label>
					<input type="text" class="form-control form-control-sm" name="to_place" value="" id="to_place">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">LR No</label>
					<input type="text" class="form-control form-control-sm" name="lr_no" value="" id="lr_no">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Truck No</label>
					<input type="text" class="form-control form-control-sm" name="vehicle_no" value="" id="vehicle_no">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Driver Mobile</label>
					<input type="text" class="form-control form-control-sm Focus" name="driver_no" value="" id="driver_no" maxlength="10" onkeypress="return isNumber(event)">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Party Contact No</label>
					<input type="text" class="form-control form-control-sm" name="party_contact" value="" id="party_contact" maxlength="10" onkeypress="return isNumber(event)">
				</div>
			</div>
		</div>

		<div class="row mt-2">
			<div class="col-xl-6 col-lg-12">
				<div class="form-group">
					<div class="box-pop bs-popover-top">
						<div class="popover-header">
							Consignor <span class="error"> * </span> 
						</div>
						<div class="popover-body height-180">
							<div class="form-group">
								<label class="control-label">Name</label>
								<input type="text" class="form-control form-control-sm" name="consignor" value="" id="consignor">
							</div>
							<div class="form-group">
								<label class="control-label">Address</label>
								<textarea class="form-control form-control-sm" name="consignor_add" id="consignor_add"></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-6 col-lg-12">
				<div class="form-group">
					<div class="box-pop bs-popover-top">
						<div class="popover-header">
							Consignee <span class="error"> * </span> 
						</div>
						<div class="popover-body height-180">
							<div class="form-group">
								<label class="control-label">Name</label>
								<input type="text" class="form-control form-control-sm" name="consignee" value="" id="consignee">
							</div>
							<div class="form-group">
								<label class="control-label">Address</label>
								<textarea class="form-control form-control-sm" name="consignee_add" id="consignee_add"></textarea>
							</div>
		                </div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Loading From <span class="danger">( For Outside Consignor )</span></label>
					<input type="text" class="form-control form-control-sm" name="loading_from" value="" id="loading_from" placeholder="Enter Loading Location">
	    			
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label><b>GST PAID BY</b></label>
					<?php echo form_dropdown('gstpaid_by', getEnumSetOptions($this->_table, 'gstpaid_by'), '', 'class="form-control form-control-sm" id="gstpaid_by"');?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label><b>I N S U R A N C E</b></label>
					<?php echo form_dropdown('insurance', getEnumSetOptions($this->_table, 'insurance'), '', 'class="form-control form-control-sm" id="insurance"');?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xl-12 col-sm-6 col-md-12">
				<h4 class="form-section"></h4>
			</div>
		</div>
		<div class="row">
			<div class="row skin skin-square">
				<div class="col-md-6">
					<div class="row skin skin-square">
		               <div class="col-xl-12 col-lg-12">
							<div class="form-group">
								<div class="box-pop bs-popover-top">
									<div class="popover-body">
										<div class="row">
											<div class="col-md-12">
												<table class="table table-bordered DataEntry">
													<thead>
														<tr>
															<th>Packages</th>
															<th>PKGS Unit</th>
															<th>Commodity</th>
														</tr>
													</thead>
													<tbody>
														<?php 
															$srNo = 0;
															
														if($srNo < 5){ ?>
															<tr class="TemplateRow">
																<td class="p-0">
																	<input type="text" class="form-control" name="packages" id="packages" maxlength="6" onkeypress="return isNumber(event)" value="" placeholder="Enter Packages"></td>
																<td class="p-0">
																	<?php 
																		echo form_dropdown('unit', getSelectOptions('units'), '12', 'class="form-control" id="unit"'); 
																	?>
																</td>
																<td class="p-0"><input type="text" class="form-control" maxlength="50" name="commodity" id="commodity" value="" placeholder="Enter Commodity"></td>
															</tr>
														<?php } ?>
													</tbody>
												</table>									
											</div>
										</div>			
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="box-pop bs-popover-top">
							<div class="popover-body p-1">
								<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label">Weight</label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="weight" value="" id="Weight" onkeyup="copyData()" onkeypress="return isNumber(event)">
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4">MT</span>
							                        </div>
							                    </div>
							                </div>
											<div class="form-group">
												<label class="control-label">Charge Weight</label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="charge_weight" value="" id="ChargeWeight" onkeypress="return isNumber(event)" onkeyup="subAmount()" >
													<div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4">MT</span>
							                        </div>
							                    </div>
							                </div>
							                
							                <div class="form-group">
												<label class="control-label">Guarantee Charges</label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="guarantee_charge" value="" id="GuaranteeCharge" onkeypress="return isNumber(event)" onkeyup="subAmount()">
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
							                        </div>
							                    </div>
							                </div>
											<div class="form-group">
												<label class="control-label">Bilty Charge</label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="bilty_charge" value="" id="BiltyCharge" onkeypress="return isNumber(event)" onkeyup="subAmount()">
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
							                        </div>
							                    </div>
							                </div>

							                <div class="form-group">
												<label class="control-label">Other Charge</label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="other_charge" value="" id="OtherCharge" onkeypress="return isNumber(event)" onkeyup="subAmount()">
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
							                        </div>
							                    </div>
							                </div>
							            </div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label">Freight Type</label>
												<?php echo form_dropdown('freight_type', $this->kaabar->getFreightType(), '', 'class="form-control select2" id="FreightType" onChange="subAmount()" ') ?>
											</div>
											<div class="form-group67">
												<label class="control-label">Freight Rate</label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="freight_rate" value="" id="FreightRate" onkeypress="return isNumber(event)" onkeyup="subAmount()">
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
							                        </div>
							                    </div>
							                </div>
											
											
											<div class="form-group">
												<label class="control-label"><b>Total Freight</b></label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="total_freight" value="" id="TotalFreight" readonly>
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
							                        </div>
							                    </div>
							                </div>
											<div class="form-group">
												<label class="control-label">Round Off</label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="roundoff" value="" id="Roundoff" readonly>
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
							                        </div>
							                    </div>
							                </div>
											<div class="form-group">
												<label class="control-label"><b>Balance</b></label>
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="balance_amt" value="" id="BalanceAmt" readonly>
							                        <div class="input-group-append">
							                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
							                        </div>
							                    </div>
							                </div>
										</div>
									
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Advance Amount</label>
											<div class="input-group input-group-sm">
												<input type="text" class="form-control form-control-sm" name="advance_amt" value="" id="AdvanceAmt" onkeypress="return isNumber(event)" onkeyup="subAmount()">
						                        <div class="input-group-append">
						                            <span class="input-group-text" id="basic-addon4"><i class="icon-rupee"></i></span>
						                        </div>
						                    </div>
						                </div>
						            </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-success" id="Add" onclick="updateLR('0')"><i class="fa fa-plus"></i> Add</button>
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
	    updateLR('0');
	});

	$(document).ready(function() {
		$('#lrSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/lrList/',
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
	    	var lr_id = $(this).val();
	    	// $('#updateParty').html();
	    	updateLR(lr_id);

	    });
	});

	function updateLR(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getLR/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.success == false){

                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
   						document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});
						$("#loader").hide();
					}
                	else
                	{

                		$('#MainForm')[0].reset();
	                	document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});

					
						$("input[name=id]").val(response.bilty.id);
	            		$("#date").val(response.bilty.date);
	            		$("#from_place").val(response.bilty.from_place);
	            		$("#to_place").val(response.bilty.to_place);
	            		$("#lr_no").val(response.bilty.lr_no);
	            		$("#vehicle_no").val(response.bilty.vehicle_no);
	            		$("#driver_no").val(response.bilty.driver_no);
	            		$("#party_contact").val(response.bilty.party_contact);
	            		$("#consignor").val(response.bilty.consignor);
	            		$("#consignee").val(response.bilty.consignee);
	            		$("#consignee_add").val(response.bilty.consignee_add);
	            		$("#consignor_add").val(response.bilty.consignor_add);
	            		$("#loading_from").val(response.bilty.loading_from);
	            		$("#gstpaid_by").val(response.bilty.gstpaid_by);
	            		$("#insurance").val(response.bilty.insurance);
	            		$("#packages").val(response.bilty.packages);
	            		$("#unit").val(response.bilty.unit);
	            		$("#commodity").val(response.bilty.commodity);
	            		$("#Weight").val(response.bilty.weight);
	            		$("#ChargeWeight").val(response.bilty.charge_weight);
	            		$("#FreightTyper").val(response.bilty.freight_type);
	            		$("#FreightRate").val(response.bilty.freight_rate);
	            		$("#GuaranteeCharge").val(response.bilty.guarantee_charge);
	            		$("#BiltyCharge").val(response.bilty.bilty_charge);
	            		$("#OtherCharge").val(response.bilty.other_charge);
	            		$("#AdvanceAmt").val(response.bilty.advance_amt);
	            		$("#TotalFreight").val(response.bilty.total_freight);
	            		$("#Roundoff").val(response.bilty.roundoff);
	            		$("#BalanceAmt").val(response.bilty.balance_amt);

	            		
	            		subAmount();
	            			

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

						  			updateLR(id);
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
								    //$("#loader").hide();
								  } 
								  else 
								  {
								  	//$("#loader").hide();
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
	$('.AddButton').click(function() {
    	var rowCount = $('.DataEntry tr').length;
    	if(rowCount > 5){
    		Swal.fire({
	          title: '(: Opps :)',
	          html:  "Only " + (rowCount-1) + " Items Allowed.",
	          icon: "error",
	        });
    		return false;
    	}
    });

	function copyData(){

	  	var weight = $("#Weight").val();
		$("#ChargeWeight").val(weight);
		console.log(weight);

    }	

    // calculate the total amount of the order
	function subAmount() {

		var weight = $("#ChargeWeight").val();
		var freight_rate = $("#FreightRate").val();

		var freight_type = $("#FreightType").val();

		var guarantee_charge = $("#GuaranteeCharge").val();
		var bilty_charge = $("#BiltyCharge").val();
		var other_charge = $("#OtherCharge").val();
		var advance_amt = $("#AdvanceAmt").val();

		if (freight_type == 'PERMT') 
		{
			var total_freight = ((Number(weight) * Number(freight_rate)) + Number(guarantee_charge) + Number(bilty_charge) + Number(other_charge));
			$("#TotalFreight").val(total_freight.toFixed(2));
		}
		else
		{
			var total_freight = (Number(freight_rate) + Number(guarantee_charge) + Number(bilty_charge) + Number(other_charge));
			$("#TotalFreight").val(total_freight.toFixed(2));
		}

		
		var grandTotal = Number(total_freight) - Number(advance_amt);

		var totalAmount =Math.round(Number(grandTotal));

		$("#BalanceAmt").val(totalAmount.toFixed(2));

		var roundoff = (Number(grandTotal) - Number(totalAmount))
		$("#Roundoff").val(roundoff.toFixed(2));
	}
</script>

