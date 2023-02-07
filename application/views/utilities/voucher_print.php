<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
	</div>
	<div class="card-body p-2 pb-0">
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<?php echo form_dropdown('invoice_type', getEnumSetOptions('invoices', 'invoice_type'), '', 'class="select2 form-control mb-0" id="invoice_type" '); ?>
				</div>
			</div>
			<div class="col-lg-3 d-none" id="invDropdown">
				<div class="form-group">
					<?php echo form_dropdown('invoice_list', [0=>'Select Invoice'], '', 'class=" select2 form-control mb-0" id="invoice_list" '); ?>
				</div>
			</div>
			
			<div class="col-md-12" id="displayInvoice">
			
			</div>
			<div class="col-lg-12">
				<div class="form-group float-right">
					<button type="button" class="btn btn-info btn-md mt-0 d-none" id="Print"><i class="fa fa-print"></i> Print</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {

		$('#invoice_type').on("change", function () {
			var type = $(this).val();
			if(type === 'SelectVoucher'){
				$('#invDropdown').addClass('d-none');
				$('#Print').addClass('d-none');
				$('#displayInvoice').html('');
				
			}else{
				$('#invDropdown').removeClass('d-none');
				$('#displayInvoice').html('<div style="color:red; font-weight:bold;" class="text-center">Please select any Invoice...</div>');
				$('#Print').addClass('d-none');
			}
			
			
			if(type) {
                $.ajax({
                    url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getInvoiceType/'+type,
                    type:"GET",
                    dataType:"json",
                    beforeSend: function(){
			            $("#loader").show();
			        },
                    success:function(data) {
                    	$("#loader").hide();
                        $('select[name="invoice_list"]').empty();
                        $('select[name="invoice_list"]').append('<option value="0">Select Invoice</option>');
                        $.each(data, function(key, value) {
                            $('select[name="invoice_list"]').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="invoice_list"]').empty();
                $('select[name="invoice_list"]').append('<option value="0">Select Invoice</option>');
            }
        });

        $('#invoice_list').on("change", function () {

        	var invoiceId = $(this).val();
        	if(invoiceId){
        		$('#displayInvoice').html('');
        		displayInvoice(invoiceId);
        	}
        	else
        	{
        		Swal.fire({
			        title: 'Opps...!',
			        html:  "Please Select Invoice First",
			        icon: "error",
			    });	
        	}
        });
	});

	function displayInvoice(id = null){

    	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getInvoice/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {
                	var sr_no = 0;
                	var my_html = '';
                	$('#displayInvoice').html('');

                	my_html += '<div class="row">';
						my_html += '<div class="col-md-12" id="">';
							my_html += '<div class="card card-default">';
								my_html += '<div class="card-header text-center">';
									my_html += '<h3 class="bold mb-0">Invoice For <?php echo "'+response.invoice.invoice_type +'" ?> - Billing</h3>';
								my_html += '</div>';
								my_html += '<div class="card-body p-0">';
									my_html += '<input type="hidden" class="form-control form-control-sm" name="type" value="">';
									my_html += '<div class="row m-0">';
										my_html += '<div class="col-lg-2 border-right border-right border-bottom">';
											my_html += '<span class="small bold">Party</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
											my_html += '<span>'+ response.invoice.ledger_name+'</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-2 border-right border-bottom">';
											my_html += '<span class="small bold">Date : </span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
											my_html += '<span>'+ response.invoice.date+'</span>';
										my_html += '</div>';
									my_html += '</div>';
					            		
					            		if(response.rows.length){
					            			
										    var result = JSON.parse(JSON.stringify(response.rows));
										    
										    var $i;		
												my_html += '<div class="table-responsive mt-4">';
											        my_html += '<table class="table table-sm table-bordered DataEntry">';
											            my_html += '<thead>';
											            	my_html += '<tr>';
											                    my_html += '<th class="text-center"></th>';
											                    my_html += '<th class="text-center">##</th>';
											                    my_html += '<th class="text-center">Charge Head</th>';
											                    my_html += '<th class="text-center">Hsn</th>';
											                    my_html += '<th class="text-center">Currancy</th>';
											                     if(response.invoice.invoice_type === 'ExportForex' || response.invoice.invoice_type === 'ImportForex')
											                     {

											                     }else{
												                    my_html += '<th class="text-center">EX Rate</th>';
												                }
											                    my_html += '<th class="text-center">Rate</th>';
											                     if(response.invoice.invoice_type === 'ExportForex' || response.invoice.invoice_type === 'ImportForex')
											                     {

											                     }else{
												                    my_html += '<th class="text-center">INR Rate</th>';
												                }
											                    my_html += '<th class="text-center">Unit</th>';
											                    my_html += '<th class="text-center">Qty</th>';
											                    my_html += '<th class="text-center">Total</th>';
											                    if(response.invoice.invoice_type === 'ExportGst' || response.invoice.invoice_type === 'ImportGst'){
												                    my_html += '<th class="text-center">GST</th>';
												                    my_html += '<th class="text-center">CGST</th>';
												                    my_html += '<th class="text-center">SGST</th>';
												                    my_html += '<th class="text-center">IGST</th>';
												                    my_html += '<th class="text-center">Total GST</th>';
												                    my_html += '<th class="text-center">Grand Total</th>';
												                }
											                    // my_html += '<th class="aligncenter"><a class="CheckAll"><i class="icon-trashcan"></i></a></th>';
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
																			my_html += '<td class="text-center grayLight SortHandle">';
														            			my_html += '<i class="icon-bars"></i>';
													            			my_html += '</td>';
																			my_html += '<td class="text-center"><span>'+item.sr_no+'</span></td>';
																			my_html += '<td class="text-center">';
																				my_html += '<span>'+item.particulars+'</span>';
																			my_html += '</td>';
																			my_html += '<td class="text-center">';
																				my_html += '<span>'+item.hsn_code+'</span>';
																			my_html += '</td>';
																			my_html += '<td class="text-center">';
																				
																				my_html += '<span>'+item.currency_name+'</span>';
																			my_html += '</td>';
																			if(response.invoice.invoice_type === 'ExportForex' || response.invoice.invoice_type === 'ImportForex')
																			{

																			}else{
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.ex_rate+'</span>';
																				my_html += '</td>';
																			}

																			my_html += '<td class="text-center">';
																				my_html += '<span>'+item.rate+'</span>';
																			my_html += '</td>';

																			if(response.invoice.invoice_type === 'ExportForex' || response.invoice.invoice_type === 'ImportForex')
																			{

																			}else{
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.inr_rate+'</span>';
																				my_html += '</td>';
																			}
																			my_html += '<td class="text-center">';
																				my_html += '<span>'+item.unit_id+'</span>';
																			my_html += '</td>';

																			my_html += '<td class="text-center">';
																				my_html += '<span>'+item.qty+'</span>';
																			my_html += '</td>';

																			my_html += '<td class="text-centermiddle">';
																				my_html += '<span>'+item.amount+'</span>';
																			my_html += '</td>';
																			if(response.invoice.invoice_type === 'ExportGst' || response.invoice.invoice_type === 'ImportGst'){
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.gst+'</span>';
																				my_html += '</td>';
																					
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.cgst+'</span>';
																				my_html += '</td>';
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.sgst+'</span>';
																				my_html += '</td>';
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.igst+'</span>';
																				my_html += '</td>';
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.gst_amount+'</span>';
																				my_html += '</td>';
																				my_html += '<td class="text-center">';
																					my_html += '<span>'+item.gross_amount+'</span>';
																				my_html += '</td>';
																			}
																			// my_html += "<td class='align-middle text-center'"+$non_stax+"'>";
																			// 	my_html +=  item.delete_btn;
																			// my_html += "</td>";
																				
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

														my_html += '</tbody>'; 
											        my_html += '</table>';
											    my_html += '</div>';
												
												my_html += '<div class="modal-footer">';
													
												my_html += '</div>';
										<?php //} ?>

									my_html += '<div class="row m-0">';
										my_html += '<div class="col-lg-2 border-right border-top border-right border-bottom">';
											my_html += '<span class="small bold">Sub Amount</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-4 border-right border-top border-bottom p-1">';
											my_html += '<span>'+ response.invoice.sub_amount+'</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-2 border-right border-top border-bottom">';
											my_html += '<span class="small bold">Additional Charges</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-4 border-right border-top border-bottom p-1">';
											my_html += '<span>'+ response.invoice.additional_charge+'</span>';
										my_html += '</div>';
										
									my_html += '</div>';
									if(response.invoice.invoice_type === 'ExportGst' || response.invoice.invoice_type === 'ImportGst'){
										my_html += '<div class="row m-0">';
											my_html += '<div class="col-lg-2 border-right border-right border-bottom">';
												my_html += '<span class="small bold">IGST</span>';
											my_html += '</div>';
											my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
												my_html += '<span>'+ response.invoice.igst+'</span>';
											my_html += '</div>';
											my_html += '<div class="col-lg-2 border-right border-bottom">';
												my_html += '<span class="small bold">SGST</span>';
											my_html += '</div>';
											my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
												my_html += '<span>'+ response.invoice.sgst+'</span>';
											my_html += '</div>';
										my_html += '</div>';
										my_html += '<div class="row m-0">';
											my_html += '<div class="col-lg-2 border-right border-right border-bottom">';
												my_html += '<span class="small bold">Total Gst</span>';
											my_html += '</div>';
											my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
												my_html += '<span>'+ response.invoice.total_gst+'</span>';
											my_html += '</div>';
											my_html += '<div class="col-lg-2 border-right border-bottom">';
												my_html += '<span class="small bold">CGST</span>';
											my_html += '</div>';
											my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
												my_html += '<span>'+ response.invoice.cgst+'</span>';
											my_html += '</div>';
										my_html += '</div>';
									}
									my_html += '<div class="row m-0">';
										my_html += '<div class="col-lg-2 border-right border-right border-bottom">';
											my_html += '<span class="small bold">Round Off</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
											my_html += '<span>'+ response.invoice.roundoff+'</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-2 border-right border-bottom">';
											my_html += '<span class="small bold">Grand Total</span>';
										my_html += '</div>';
										my_html += '<div class="col-lg-4 border-right border-bottom p-1">';
											my_html += '<span>'+ response.invoice.net_amount+'</span>';
										my_html += '</div>';
									my_html += '</div>';
                   
					if(my_html){
                        $('#displayInvoice').append(my_html);
                        $('#Print').removeClass('d-none');
                    }
                    $("#loader").hide();

					$('#Print').on('click', function(e) {
						e.preventDefault();
						window.location.href = "<?php echo base_url('accounts/export_billing/gst/print/') ?>"+id+"/1";
						return false;
					});
				}
            },
            error: function (result) {
                $("#loader").hide();
            },
        });
	}
</script>