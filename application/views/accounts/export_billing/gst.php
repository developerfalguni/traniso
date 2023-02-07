<?php
	echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"');
	echo form_hidden($id);
?>
<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
	</div>
	<div class="card-body p-2 pb-0">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group row">
					<label class="col-sm-3 col-form-label col-form-label-sm">Select Invoice : </label>
					<div class="col-sm-9">
						<?php echo form_dropdown('invoice_list', [], '', 'class="form-control" id="exportInvoiceSelect2"'); ?>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group row">
					<label class="col-sm-3 col-form-label col-form-label-sm">Job No : </label>
					<div class="col-sm-9" id="JobRemove">
						<?php echo form_dropdown('job_list', [], '', 'class="form-control mb-0" id="jobSelect2"'); ?>
					</div>
					<div class="col-sm-9 d-none" id="JobText">
					</div>
				</div>
				<div class="form-group">
					
				</div>
			</div>
		</div>	
		<div class="row">			
			<div class="col-md-12" id="">
				<div class="card card-default">
					<div class="card-header text-center">
						<h3 class="bold mb-0">Invoice for <?php echo $type ?> - GST Billing</h3>
					</div>
					<div class="card-body p-0">
						<input type="hidden" class="form-control form-control-sm" name="type" value="">
						<div class="row m-0">
							<div class="col-lg-2 border-right border-right border-bottom">
								<span class="small bold">Party</span>
							</div>
							<div class="col-lg-4 border-right border-bottom p-1">
								<input type="text" class="form-control form-control-sm" name="billingParty" value="" size="30" id="billingParty">
								<input type="hidden" name="billingParty_id" title="id" id="billingParty_id" value="">
								<input type="hidden" name="billingParty_category" title="category" id="billingParty_category" value="">
							</div>
							<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Date : </span>
						</div>
						<div class="col-lg-4 border-right border-bottom p-1">
							<div class="input-group input-group-sm" id="date">
								<input type="text" class="form-control DatePicker" name="date" value="<?php echo date('d-m-Y') ?>" id="dateValue">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
						</div>
						
						<div class="table-responsive" id="updateInvoice">
					        <table class="table table-sm table-bordered DataEntry">
					            <thead>
					            	<tr>
					                    <th class="text-center align-middle"></th>
					                    <th class="text-center width-50 align-middle">##</th>
					                    <th class="align-middle width-200">Charge Head</th>
					                    <th class="align-middle width-80">Hsn</th>
					                    <th class="align-middle text-center width-80">Currancy</th>
					                    <th class="align-middle text-center width-80">EX Rate</th>
					                    <th class="align-middle text-center width-80">Rate</th>
					                    <th class="align-middle text-center width-80">INR Rate</th>
					                    <th class="align-middle text-center width-80">Unit</th>
					                    <th class="align-middle text-center width-80">Qty</th>
					                    <th class="align-middle text-center width-80">Total</th>
					                    <th class="text-center align-middle width-80">GST</th>
					                    <th class="text-center align-middle width-80">CGST</th>
					                    <th class="text-center align-middle width-80">SGST</th>
					                    <th class="text-center align-middle width-80">IGST</th>
					                    <th class="text-center align-middle width-80">Total GST</th>
					                    <th class="text-center align-middle width-80">Grand Total</th>
					                    <th class="aligncenter"><a class="CheckAll"><i class="icon-trashcan"></i></a></th>
					               </tr>
				            	</thead>
				            	<tbody>
					            	<?php 
					            	$sr_no = 1;
					            	?>
					            	<tr class="TemplateRow">

					            		<td class="align-middle text-center grayLight SortHandle max-60">
					            			<i class="icon-bars"></i>
					            			<input type="hidden" class="InvoiceItemId" value="" name="kbr_invoice_item_id[]">
					            			<input type="hidden" class="JobItemId" value="" name="kbr_job_item_id[]">
					            		</td>
										<td class="align-middle max-60"><input type="text" class="form-control form-control-sm Numeric Validate text-center SrNo Increment" name="kbr_sr_no[]" value="<?php //echo $sr_no ?>"></td>
										<td class="align-middle min-200">
											<input type="text" class="form-control form-control-sm BillItemCode" value="" name="kbr_bill_item_code[]" required>
											<input type="hidden" class="form-control form-control-sm BillItemID" name="kbr_bill_item_id[]" value="">
										</td>
										<td class="align-middle">
											<input type="text" class="form-control form-control-sm HSNCode" onkeypress="return isNumber(event)" value="" name="kbr_hsn_code[]" required>
										</td>
										<td class="align-middle">
											<?php echo form_dropdown('kbr_is_inr[]', array('Yes'=>'Yes', 'No'=>'No'), '', 'class="form-control form-control-sm Unchanged IsINR d-none"'); ?>
											<input type="text" class="form-control form-control-sm CurrencyName Unchanged Validate" value="" name="kbr_currency_name[]" required>
											<input type="hidden" class="Unchanged CurrencyNameId" value="" name="kbr_currency[]">
										</td>
										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Unchanged ExchangeRate Validate text-right" value="" name="kbr_ex_rate[]" onkeypress="return isNumberDot(event)" required>
											<input type="hidden" class="form-control form-control-sm Unchanged Numeric Currency text-right" name="kbr_currency_amt[]" value="" onkeypress="return isNumberDot(event)" >
										</td>

										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric Rate" onkeypress="return isNumberDot(event)" name="kbr_rate[]" 	value="" required>
										</td>

										<td class="align-middle">
											<input type="text" class="form-control form-control-sm InrRate Numeric" name="kbr_inr_rate[]" readonly value="" required>
										</td>

										<td class="align-middle">
											<?php echo form_dropdown('kbr_unit[]', getSelectOptions('units', 'code'), '', 'class="Unchanged form-control form-control-sm Unit"'); ?>
										</td>

										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric Units Validate" name="kbr_qty[]" value="" onkeypress="return isNumberDot(event)" required>
										</td>

										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric Amount Validate" name="kbr_amount[]" readonly value="" required>
										</td>

										<td class="align-middle">
											<?php
												echo form_dropdown('kbr_gst[]', getEnumSetOptions('invoices', 'gst_per'), '', 'class="Unchanged form-control form-control-sm GST"');
											?>
										</td>
										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric CGST Validate" name="kbr_cgst[]" readonly value="" required>
										</td>
										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric SGST Validate" name="kbr_sgst[]" readonly value="" required>
										</td>
										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric IGST Validate" name="kbr_igst[]" readonly value="" required>
										</td>
										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric GSTAmount Validate" name="kbr_gst_amount[]" readonly value="" required>
										</td>
										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric NetAmount Validate" name="kbr_gross_amount[]" readonly value="" required>
										</td>
										
										<td><button type="button" class="btn btn-danger btn-sm DelButton" disabled><i class="icon-minus fa fa-minus"></i></button></td>
									</tr>
									<?php $sr_no++; ?>					
								</tbody>
							</table>
						
							<button type="button" class="btn btn-success btn-sm AddButton m-2"><i class="icon-white icon-plus"></i> New Item</button>
						</div>

						<div class="row mt-2 m-0">
							<div class="col-lg-2 border">
								<span class="small bold">Sub Amount</span>
							</div>
							<div class="col-lg-4 border-right border-bottom border-top p-1">
								<input type="text" class="form-control form-control-sm" name="sub_amount" id="SubAmount" value="" readonly>
							</div>
							<div class="col-lg-2 border-right border-bottom border-top">
								<span class="small bold">CGST</span>
							</div>
							<div class="col-lg-4 border-right border-bottom border-top p-1">
								<input type="text" class="form-control form-control-sm" name="cgst_amount"  id="Cgst" value="" readonly>
							</div>
						</div>
						<div class="row m-0">
							<div class="col-lg-2 border-right border-left border-bottom">
								<span class="small bold">IGST</span>
							</div>
							<div class="col-lg-4 border-right border-bottom p-1">
								<input type="text" class="form-control form-control-sm" name="igst_amount" id="Igst" value="" readonly>
							</div>
							<div class="col-lg-2 border-right border-bottom">
								<span class="small bold">SGST</span>
							</div>
							<div class="col-lg-4 border-right border-bottom p-1">
								<input type="text" class="form-control form-control-sm" name="sgst_amount" id="Sgst" value="" readonly>
							</div>
						</div>
						<div class="row m-0">
							<div class="col-lg-2 border-right border-left border-bottom">
								<span class="small bold">Total GST</span>
							</div>
							<div class="col-lg-4 border-right border-bottom p-1">
								<input type="text" class="form-control form-control-sm" name="total_gst" id="TotalGst" value="" readonly>
							</div>
							<div class="col-lg-2 border-right border-bottom">
								<span class="small bold">Additional charges</span>
							</div>
							<div class="col-lg-4 border-right border-bottom p-1">
								<input type="text" class="form-control form-control-sm Charge" name="additional_charge" id="AdditionalCharge" value="" onkeypress="return isNumber(event)">
							</div>
						</div>
						<div class="row m-0">
							<div class="col-lg-2 border-right border-left border-bottom">
								<span class="small bold">Round Off</span>
							</div>
							<div class="col-lg-4 border-right border-bottom p-1">
								<input type="text" class="form-control form-control-sm" id="RoundOff" name="roundoff" value="" onkeypress="return isNumber(event)">
							</div>
							<div class="col-lg-2 border-right border-bottom">
								<span class="small bold">Grand Total</span>
							</div>
							<div class="col-lg-4 border-right border-bottom p-1">
								<input type="text" class="form-control form-control-sm" name="net_amount" id="NetAmount" value="" readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="row d-none" id="einvoiceSection">
				<div class="col-md-2">
					<img width="150" id="signedInvQR" class="width-192" alt="eInvoice QR">
				</div>
				<div class="col-md-10">
					<div class="row m-0">
						<div class="col-lg-2">
							<span class="small bold">Ack No</span>
						</div>
						<div class="col-lg-10">
							<span class="small" id="ackNo"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2">
							<span class="small bold">Ack Date</span>
						</div>
						<div class="col-lg-10">
							<span class="small" id="ackDate"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2">
							<span class="small bold">IRN No</span>
						</div>
						<div class="col-lg-10">
							<span class="small" id="IrnNo"></span>
						</div>
					</div>
					<div id="CnlRsnDiv">
						<div class="row m-0">
							<div class="col-lg-2">
								<span class="small bold">Cancel Date</span>
							</div>
							<div class="col-lg-10">
								<span class="small" id="CancelDate"></span>
							</div>
						</div>
						<div class="row m-0">
							<div class="col-lg-2">
								<span class="small bold">Cancel Reason</span>
							</div>
							<div class="col-lg-10">
								<span class="small" id="CnlReason"></span>
							</div>
						</div>
					</div>
					<div class="row m-0 mt-3">
						<div class="col-lg-12">
							<button type="button" class="btn btn-danger btn-sm" id="cancelInv"><i class="feather icon-remove"></i> Cancel eInvoice</button>
						</div>
					</div>
				</div>
			</div>
			<div class="row d-none" id="einvoiceBtn">
				<div class="col-md-12">
					<button type="button" class="btn btn-danger" id="eInvoice"><i class="feather icon-file"></i> Generate eInvoice</button>	
				</div>
			</div>
		</div>
		
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-left pt-2">
					<button type="button" class="btn btn-warning" id="Print"><i class="feather icon-printer"></i> Print</button>
					<button type="button" class="btn btn-success" id="Create"><i class="fa fa-plus"></i> Create Invoice</button>
				</div>
				<div class="float-right pt-2">
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button type="button" class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Delete</button>
					<button type="button" class="btn btn-danger" id="Reset"><i class="feather icon-refresh"></i> Reset</button>
				</div>
			</div>
		</div>
	</div>
</div>
</form>

<script type="text/javascript">
	$(document).ready(function() {
		///////// Autocomplete Bill Item
		$('.BillItemCode').cskaabar_autocomplete({source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>'});
		$('.CurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.IsINR'});

		$('#billingParty').kaabar_autocomplete({source: '<?php echo site_url('master/operation/party/ajax2') ?>', alertText: 'Billing Party'});

		select2init()
	});

	function select2init(){

		$('#exportInvoiceSelect2').select2({
			placeholder: "Select Export Invoice",
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/invoiceList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						searchTerm: params.term // search term
					};
				},
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
	    	var invoice_id = $(this).val();
	    	updateInvoice(invoice_id);
	    });

	    $('#jobSelect2').select2({
	    	placeholder: "Select Export Job",
	    	ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/jobsList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						searchTerm: params.term // search term
					};
				},
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
	    	var job_id = $(this).val();
	    	$('#MainForm')[0].reset();
			$("input[name=id]").attr('value', '0');
			document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
			    tr.remove();
			});

	    	updateJob(job_id);
	    });
	}

	function updateInvoice(id = null)
    {   
    	if(id){
	    	$.ajax({
	    		url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getInvoice/'+id,
	            type: 'post',
	            dataType: 'json',
	            cache: true,
	            beforeSend: function(){
		            $("#loader").show();
		        },
	            success:function(response) {

	            	var sr_no = 0;
	                var my_html = '';
	            	
	            	$('#MainForm')[0].reset();
	        		$("input[name=id]").attr('value', '0');
	        		document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
					    tr.remove();
					});

	        		$('#JobRemove').addClass('d-none');
	        		$('#JobText').removeClass('d-none');
	        		if(response.invoice.jobNo)
	            			$('#JobText').html('<span>'+response.invoice.jobNo+'</span>');
	            		else
	            			$('#JobText').html('<span>N/A</span>');

	        		$("input[name=id]").val(response.invoice.id);
	        		$("#billingParty").val(response.invoice.ledger_name);
	        		$("#billingParty_id").val(response.invoice.ledger_id);
	        		$("#billingParty_category").val(response.invoice.ledger_category);

	        		$("#dateValue").val(response.invoice.date);
	        		$("#AdditionalCharge").val(response.invoice.additional_charge);
	        		$("#RoundOff").val(response.invoice.roundoff);

	        		if(response.rows.length){
					    var result = JSON.parse(JSON.stringify(response.rows));
					    var $i;

					    if(result){
						    for(var i=0;i < result.length;i++){
						        var item = result[i]; 
						        if(item.id > 0) {
						        	var $non_stax = '';
						        	if(item.stax_code){
										if (item.stax_code.length == 0){
											$non_stax = 'NonSTax';
										}
						        	}
						        	my_html += '<tr>';
										my_html += '<td class="align-middle text-center grayLight SortHandle">';
					            			my_html += '<i class="icon-bars"></i>';
					            			my_html += '<input type="hidden" value="'+item.id+'" name="invoice_item_id['+item.id+']">';
					            			my_html += '<input type="hidden" value="'+item.job_costsheet_id+'" name="job_item_id['+item.id+']">';
					            		my_html += '</td>';
										my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm Numeric Validate text-center" name="sr_no['+item.id+']" value="'+item.sr_no+'"></td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm BillItemCode" value="'+item.particulars+'" name="bill_item_code['+item.id+']" required>';
											my_html += '<input type="hidden" class="form-control form-control-sm BillItemID" name="bill_item_id['+item.id+']" value="'+item.bill_item_id+'">';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm HSNCode" onkeypress="return isNumber(event)" value="'+item.hsn_code+'" name="hsn_code['+item.id+']" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += item.is_inr;
											my_html += '<input type="text" class="form-control form-control-sm CurrencyName Unchanged Validate" value="'+item.currency_name+'" name="currency_name['+item.id+']" required>';
											my_html += '<input type="hidden" class="Unchanged CurrencyNameId" value="'+item.currency_id+'" name="currency['+item.id+']">';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Unchanged ExchangeRate Validate text-right" value="'+item.ex_rate+'" name="ex_rate['+item.id+']" onkeypress="return isNumberDot(event)" required>';
											my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric Currency text-right" name="currency_amt['+item.id+']" value="'+item.currency_amt+'" onkeypress="return isNumberDot(event)" >';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric Rate" onkeypress="return isNumberDot(event)" name="rate['+item.id+']" 	value="'+item.rate+'" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm InrRate Numeric" name="inr_rate['+item.id+']" readonly value="'+item.inr_rate+'" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											///// Dropdown units
											my_html += item.unit_id;
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric Units Validate" name="qty['+item.id+']" value="'+item.qty+'" onkeypress="return isNumberDot(event)" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric Amount Validate" name="amount['+item.id+']" readonly value="'+item.amount+'" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											////// GST DROPDOWN
											my_html += item.gst;
										my_html += '</td>';
											
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric CGST Validate" name="cgst['+item.id+']" readonly value="'+item.cgst+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric SGST Validate" name="sgst['+item.id+']" readonly value="'+item.sgst+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric IGST Validate" name="igst['+item.id+']" readonly value="'+item.igst+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric GSTAmount Validate" name="gst_amount['+item.id+']" readonly value="'+item.gst_amount+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric NetAmount Validate" name="gross_amount['+item.id+']" readonly value="'+item.gross_amount+'" required>';
										my_html += '</td>';

										my_html += "<td class='align-middle text-center'"+$non_stax+"'>";
											my_html +=  item.delete_btn;
										my_html += "</td>";
											
									my_html += '</tr>';
									sr_no = item.sr_no;
								}
							}
						}
					}

					if(response.invoice.einv_sts){

						$("span#ackNo").text(response.invoice.einv.ack_no);
						$("span#ackDate").text(response.invoice.einv.ack_date);
						$("span#IrnNo").text(response.invoice.einv.irn_no);
						$("span#CnlReason").text(response.invoice.einv.cancel_rsn);
						$("span#CancelDate").text(response.invoice.einv.cancel_date);


						if(response.invoice.einv.status == 'ACT'){
							signedInvQR.setAttribute('src', "data:image/png;base64," + response.invoice.einv.QrCodeImage);
							$('#cancelInv').removeClass('d-none');
							$('#CnlRsnDiv').addClass('d-none');
							
						}
						else{
							signedInvQR.setAttribute('src', "data:image/png;base64," + response.invoice.einv.cnl_img);
							$('#cancelInv').addClass('d-none');
							$('#CnlRsnDiv').removeClass('d-none');
						}

						$('div#einvoiceSection').removeClass('d-none');
						$('div#einvoiceBtn').addClass('d-none');
						
					}
					else
					{
						$('div#einvoiceBtn').removeClass('d-none');
						$('div#einvoiceSection').addClass('d-none');
					}

	        		$("#loader").hide();
	            	

					if(my_html){
	                    $('.TemplateRow:last-child').before(my_html);
	                    //getTotal();
	                    $('.TemplateRow:last-child').find('.SrNo').val(parseInt(sr_no)+parseInt(1));
					}

				
	            },
	            error: function (result) {
	                $("#loader").hide();
	            },
	        });
		}
        else
        {
           	$('#MainForm')[0].reset();
    		$("input[name=id]").attr('value', '0');
    		document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
			    tr.remove();
			});
			$("#loader").hide();
			return false;
		}
    }

    $('#Print').on('click', function(e) {
		e.preventDefault();	
		var id = $("input[name=id]").val();
		
		if(id > 0){
			let a= document.createElement('a');
			a.target= '_blank';
			a.href= "<?php echo base_url($this->_clspath.$this->_class.'/pdf/1/'); ?>"+id;
			a.click();
		}
		else
		{
			Swal.fire({
		        html:  "Please select any Invoice for print...!",
		        icon: "error",
		    });
		}
	});

	$('#Reset').on('click', function(e) {
		e.preventDefault();	
		
		updateInvoice(0);
		$('input').removeClass('is-valid');
		$('input').removeClass('is-invalid');
		$('#billingParty').removeAttr('readonly');
		$('#JobRemove').removeClass('d-none');
	    $('#JobText').addClass('d-none');

	    $("span#ackNo").text('');
		$("span#ackDate").text('');
		$("span#IrnNo").text('');

		signedInvQR.setAttribute('src', "");
		
		$('div#einvoiceSection').addClass('d-none');
		$('div#einvoiceBtn').removeClass('d-none');
		
		if ($('#exportInvoiceSelect2').data('select2')) {
			$('#exportInvoiceSelect2').val(null).empty().select2('destroy')
		}
		if ($('#jobSelect2').data('select2')) {

			$('#jobSelect2').val(null).empty().select2('destroy')
			
		}

		select2init()
		return false;

	});

	$('#Create').on('click', function(e) {
		e.preventDefault();	
		
		updateInvoice(0);
		$('input').removeClass('is-valid');
		$('input').removeClass('is-invalid');
		$('#billingParty').removeAttr('readonly');
		$('#JobRemove').removeClass('d-none');
	    $('#JobText').addClass('d-none');
		
		$("span#ackNo").text('');
		$("span#ackDate").text('');
		$("span#IrnNo").text('');

		signedInvQR.setAttribute('src', "");
		
		$('div#einvoiceSection').addClass('d-none');
		$('div#einvoiceBtn').removeClass('d-none');
		

		if ($('#exportInvoiceSelect2').data('select2')) {
			$('#exportInvoiceSelect2').val(null).empty().select2('destroy')
		}
		if ($('#jobSelect2').data('select2')) {
			$('#jobSelect2').val(null).empty().select2('destroy')
		}
		select2init()
		return false;
	});

	$('#eInvoice').on('click', function(e) {
		e.preventDefault();	
		Swal.fire({
			title: "Are you sure..?",
			text: "You wont to Generate eInvoice...?",
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes",
			cancelButtonText: "Cancel",
			buttonsStyling: true
		}).then(function(result) {

			if (result.dismiss === Swal.DismissReason.cancel) {
				return false;
			}
			else if (result.value) {
				
				var voucher_id = $("input[name=id]").val();

				if(voucher_id){
		        	$.ajax({
		                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/einvoice',
		                type: 'post',
		                dataType: 'json',
		                data: {
		                	voucher_id: voucher_id,
		                },
		                beforeSend: function(){
				            $("#loader").show();
				        },
		                success:function(response) {
		                	if(response.success === true) {
                                $("#loader").hide();
                                Swal.fire({
                                    html:  ""+response.messages+"",
                                    icon: "success",
                                }).then(function() {
                                	updateInvoice(voucher_id)
                                });
                                
                            }
                            else
                            {
                                $("#loader").hide();
                                if (Array.isArray(response.messages)) {
                                	var msg = response.messages;
                                	var msgdis = '';
                                	msgdis = msg.join("\r\n")
                                	
                                	new PNotify({
										title: '<i class="fa fa-exclamation-circle"> Error...!',
										text: msgdis,
										type: 'error',
										nonblock: {
											nonblock: true,
											nonblock_opacity: .2
										}
									});
								}
								else
								{
									new PNotify({
										title: '<i class="fa fa-exclamation-circle"> Error...!',
										text: response.messages,
										type: 'error',
										nonblock: {
											nonblock: true,
											nonblock_opacity: .2
										}
									});
								}



                            }

                            return false;
		                },
		                error: function (result) {
		                    $("#loader").hide();
		                    new PNotify({
								title: '<i class="fa fa-exclamation-circle"> Error...!',
								text: 'Something wrong, Please try again...!',
								type: 'error',
								nonblock: {
									nonblock: true,
									nonblock_opacity: .2
								}
							});
		                },
		            });
		        }
		        else
		        {
		           	Swal.fire({
                        html:  "Please Select atleast 1 Invoice...!",
                        icon: "warning",
                    })
					$("#loader").hide();
					return false;
				}


			}
		});
        return false;
		

		
	});


	$('#cancelInv').on('click', function(e) {
		e.preventDefault();	

		Swal.fire({
		  	title: "Sure You want to Cancel eInvoice...?",
			icon: "warning",
		  	html: `

		  		<div class="form-group">
					<input type="hidden" readonly="readonly" class="swal2-input swal2-input-sm" id="Irn" value="">
				</div>
				<div class="form-group">
					<select class="swal2-input" id="CnlRsn">
						<option value="1">Duplicate</option>
						<option value="2">Data entry mistake</option>
						<option value="3">Order Cancelled</option>
						<option value="4">Others</option>
					</select>
				</div>`,
		  	focusConfirm: false,
		  	showCancelButton: true,
		  	confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, Cancel",
			cancelButtonText: "Close",
			buttonsStyling: true,
		}).then((result) => {
		 	
			var voucher_id = $("input[name=id]").val();
			var irn_no = $("span#IrnNo").text();
			var cancel_rsn = $(CnlRsn).val();
			var cancel_rmk = $(CnlRsn).attr('id');
			var cancel_remark = $('#'+cancel_rmk+' option:selected').text();
			
			if(voucher_id){

	        	$.ajax({
	                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/caneinvoice',
	                type: 'post',
	                dataType: 'json',
	                data: {
	                	voucher_id: voucher_id,
	                	irn_no: irn_no,
	                	cancel_rsn: cancel_rsn,
	                	cancel_remark: cancel_remark,
	                },
	                beforeSend: function(){
			            $("#loader").show();
			        },
	                success:function(response) {
	                	if(response.success === true) {
                            $("#loader").hide();
                            Swal.fire({
                                html:  ""+response.messages+"",
                                icon: "warning",
                            }).then(function() {
                            	updateInvoice(voucher_id)
                            });
                            
                        }
                        else
                        {
                            $("#loader").hide();

                            if (Array.isArray(response.messages)) {
                            	var msg = response.messages;
                            	var msgdis = '';
                            	msgdis = msg.join("\r\n")
                            	
                            	new PNotify({
									title: '<i class="fa fa-exclamation-circle"> Error...!',
									text: msgdis,
									type: 'error',
									nonblock: {
										nonblock: true,
										nonblock_opacity: .2
									}
								});
							}
							else
							{
								new PNotify({
									title: '<i class="fa fa-exclamation-circle"> Error...!',
									text: response.messages,
									type: 'error',
									nonblock: {
										nonblock: true,
										nonblock_opacity: .2
									}
								});
							}



                        }

                        return false;
	                },
	                error: function (result) {
	                    $("#loader").hide();
	                    new PNotify({
							title: '<i class="fa fa-exclamation-circle"> Error...!',
							text: 'Something wrong, Please try again...!',
							type: 'error',
							nonblock: {
								nonblock: true,
								nonblock_opacity: .2
							}
						});
	                },
	            });
	        }
	        else
	        {
	           	Swal.fire({
                    html:  "Please Select atleast 1 Invoice...!",
                    icon: "warning",
                })
				$("#loader").hide();
				
			}

			return false;

		});

    	return false;

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

            		$("#billingParty").val(response.job.billing_party_name);
            		$("#billingParty_id").val(response.job.billing_party_id);
            		$("#billingParty_category").val(response.job.billing_party_category);
            		$("#billingParty").attr('readonly', 'readonly');
            		$("#dateValue").val("<?php echo date('d-m-Y') ?>");

            		document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
					    tr.remove();
					});
                	// console.log(response);
                	if(response.rows.length){
					    var result = JSON.parse(JSON.stringify(response.rows));
					    var $i;
					    var my_html;

					    if(result){

						    for(var i=0;i < result.length;i++){
						        var item = result[i]; 
						        if(item.id > 0) {
						        	var $non_stax = '';
						        	if(item.stax_code){
										if (item.stax_code.length == 0){
											$non_stax = 'NonSTax';
										}
						        	}
						        	my_html += '<tr>';
										my_html += '<td class="align-middle text-center grayLight SortHandle">';
					            			my_html += '<i class="icon-bars"></i>';
					            			my_html += '<input type="hidden" class="InvoiceItemId" value="" name="kbr_invoice_item_id['+item.id+']">';
					            			my_html += '<input type="hidden" class="JobItemId" value="'+item.id+'" name="kbr_job_item_id['+item.id+']">';
					            		my_html += '</td>';
										my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm Numeric Validate text-center" name="kbr_sr_no['+item.id+']" value="'+item.sr_no+'"></td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm BillItemCode" value="'+item.bill_item_name+'" name="kbr_bill_item_code['+item.id+']" required>';
											my_html += '<input type="hidden" class="form-control form-control-sm BillItemID" name="kbr_bill_item_id['+item.id+']" value="'+item.bill_item_id+'">';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm HSNCode" onkeypress="return isNumber(event)" value="'+item.hsn_code+'" name="kbr_hsn_code['+item.id+']" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += item.sell_is_inr;
											my_html += '<input type="text" class="form-control form-control-sm CurrencyName Unchanged Validate" value="'+item.sell_currency_name+'" name="kbr_currency_name['+item.id+']" required>';
											my_html += '<input type="hidden" class="Unchanged CurrencyNameId" value="'+item.sell_currency_id+'" name="kbr_currency['+item.id+']">';
										my_html += '</td>';


										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Unchanged ExchangeRate Validate text-right" value="'+item.sell_ex_rate+'" name="kbr_ex_rate['+item.id+']" onkeypress="return isNumberDot(event)" required>';
											my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric Currency text-right" name="kbr_currency_amt['+item.id+']" value="'+item.sell_currency_amt+'" onkeypress="return isNumberDot(event)" >';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric Rate" onkeypress="return isNumberDot(event)" name="kbr_rate['+item.id+']" 	value="'+item.sell_rate+'" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm InrRate Numeric" name="kbr_inr_rate['+item.id+']" readonly value="'+item.sell_inr_rate+'" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											///// Dropdown units
											my_html += item.sell_unit_id;
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric Units Validate" name="kbr_qty['+item.id+']" value="'+item.sell_qty+'" onkeypress="return isNumberDot(event)" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric Amount Validate" name="kbr_amount['+item.id+']" readonly value="'+item.sell_amount+'" required>';
										my_html += '</td>';

										my_html += '<td class="align-middle">';
											my_html += item.gst;
										my_html += '</td>';
											
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric CGST Validate" name="kbr_cgst['+item.id+']" readonly value="'+item.cgst+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric SGST Validate" name="kbr_sgst['+item.id+']" readonly value="'+item.sgst+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric IGST Validate" name="kbr_igst['+item.id+']" readonly value="'+item.igst+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric GSTAmount Validate" name="kbr_gst_amount['+item.id+']" readonly value="'+item.gst_amount+'" required>';
										my_html += '</td>';
										my_html += '<td class="align-middle">';
											my_html += '<input type="text" class="form-control form-control-sm Numeric NetAmount Validate" name="kbr_gross_amount['+item.id+']" readonly value="'+item.gross_amount+'" required>';
										my_html += '</td>';

										my_html += "<td class='align-middle text-center'"+$non_stax+"'>";
											my_html +=  item.delete_btn;
										my_html += "</td>";
											
									my_html += '</tr>';
									sr_no = item.sr_no;
								}
							}
						}
					}
            		$("#loader").hide();
            		if(my_html){
	                    $('.TemplateRow:last-child').before(my_html);
	                    $('.TemplateRow:last-child').find('.SrNo').val(parseInt(sr_no)+parseInt(1));
					}

					
                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
           	$('#MainForm')[0].reset();
    		$("input[name=id]").attr('value', '0');
    		document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
			    tr.remove();
			});
			$("#loader").hide();
			return false;
		}
    }

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
	}

	// Data Entry +/-
	var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';

	////// Focus On Select All
	$('body').on('click', 'input[type="text"]', function(e) {
       $(this).select();
	});

	$('body').on('click', 'button.AddButton', function(e) {

		var submit_row = $(this).hasClass('RowSubmit');
		var clone_row  = true;
		$template_row  = $(this).parents('#updateInvoice').find('table.DataEntry').find('tr.TemplateRow');
		$validate_row  = $(this).parents('#updateInvoice').find('table.DataEntry').find('tr.lastRowElement');

		$template_row.find('.Validate').each(function(index, el) {
			if ($(this).val() === '') {
				clone_row = false;
				$(this).addClass('is-invalid');
			}
			else {
				$(this).removeClass('is-invalid');
			}
		});

		if (clone_row) {
			
			cloneRow($template_row);
			$('.BillItemCode').cskaabar_autocomplete({source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>'});
			$('.CurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.IsINR'});
			if (submit_row === false) {
				e.preventDefault();
			}
		}
		else {
			e.preventDefault();
		}
	});

	////// Existing Row Remove Confirmation
	$('body').on('change','input.DeleteCheckbox',function(e){	
    	e.preventDefault();
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

    $('body').on('keyup', '.ExchangeRate', function() {

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

		var gst = $(this).parents('tr').children('td').find('.GST').val();
		var gst_amount = amount * gst / 100;
		var statecode = 24;

		if(statecode == 24){
			var cgst = gst_amount / 2;
			var sgst = gst_amount / 2;
			var igst = 0;
		}
		else
		{
			var cgst = 0;
			var sgst = 0;
			var igst = gst_amount;
		}

		var new_amount = parseFloat(amount)+parseFloat(gst_amount);

		$(this).parents('tr').children('td').find('.CGST').val(cgst);
		$(this).parents('tr').children('td').find('.SGST').val(sgst);
		$(this).parents('tr').children('td').find('.IGST').val(igst);
		$(this).parents('tr').children('td').find('.GSTAmount').val(gst_amount);
		$(this).parents('tr').children('td').find('.NetAmount').val(new_amount);
		getTotal();
	});

	$('body').on('keyup', '.Units', function() {
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

		var gst = $(this).parents('tr').children('td').find('.GST').val();
		var gst_amount = amount * gst / 100;
		var statecode = 24;

		if(statecode == 24){
			var cgst = gst_amount / 2;
			var sgst = gst_amount / 2;
			var igst = 0;
		}
		else
		{
			var cgst = 0;
			var sgst = 0;
			var igst = gst_amount;
		}

		var new_amount = parseFloat(amount)+parseFloat(gst_amount);

		$(this).parents('tr').children('td').find('.CGST').val(cgst);
		$(this).parents('tr').children('td').find('.SGST').val(sgst);
		$(this).parents('tr').children('td').find('.IGST').val(igst);
		$(this).parents('tr').children('td').find('.GSTAmount').val(gst_amount);
		$(this).parents('tr').children('td').find('.NetAmount').val(new_amount);

		getTotal();
	});

	$('body').on('keyup', '.Rate', function() {
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

		var gst = $(this).parents('tr').children('td').find('.GST').val();
		var gst_amount = amount * gst / 100;
		var statecode = 24;

		if(statecode == 24){
			var cgst = gst_amount / 2;
			var sgst = gst_amount / 2;
			var igst = 0;
		}
		else
		{
			var cgst = 0;
			var sgst = 0;
			var igst = gst_amount;
		}

		var new_amount = parseFloat(amount)+parseFloat(gst_amount);

		$(this).parents('tr').children('td').find('.CGST').val(cgst);
		$(this).parents('tr').children('td').find('.SGST').val(sgst);
		$(this).parents('tr').children('td').find('.IGST').val(igst);
		$(this).parents('tr').children('td').find('.GSTAmount').val(gst_amount);
		$(this).parents('tr').children('td').find('.NetAmount').val(new_amount);


		getTotal();
	});

	$('body').on('change', '.GST', function() {
		var ex_rate = $(this).parents('tr').children('td').find('.ExchangeRate').val();
		var rate    = $(this).parents('tr').children('td').find('.Rate').val();
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

		var gst = $(this).val();
		var gst_amount = amount * gst / 100;
		var statecode = 24;

		if(statecode == 24){
			var cgst = gst_amount / 2;
			var sgst = gst_amount / 2;
			var igst = 0;
		}
		else
		{
			var cgst = 0;
			var sgst = 0;
			var igst = gst_amount;
		}

		var new_amount = parseFloat(amount)+parseFloat(gst_amount);

		$(this).parents('tr').children('td').find('.CGST').val(cgst);
		$(this).parents('tr').children('td').find('.SGST').val(sgst);
		$(this).parents('tr').children('td').find('.IGST').val(igst);
		$(this).parents('tr').children('td').find('.GSTAmount').val(gst_amount);
		$(this).parents('tr').children('td').find('.NetAmount').val(new_amount);


		getTotal();
	});

	$('body').on('keyup', '.Charge', function() {
		getTotal();
	});

	function getTotal(){
		var total_amount = 0;
	    $(".Amount").each(function(){
	        total_amount += +$(this).val();
	    });

	    var total_cgst = 0;
	    $(".CGST").each(function(){
	        total_cgst += +$(this).val();
	    });

	    var total_sgst = 0;
	    $(".SGST").each(function(){
	        total_sgst += +$(this).val();
	    });

	    var total_igst = 0;
	    $(".IGST").each(function(){
	        total_igst += +$(this).val();
	    });

	    var total_gst = 0;
	    $(".GSTAmount").each(function(){
	        total_gst += +$(this).val();
	    });

	    var total_net_amount = 0;
	    $(".NetAmount").each(function(index, value){

	    	total_net_amount += $(value).val();
	    });

	    var add_charge = $("#AdditionalCharge").val();
	    if(!add_charge)
	    	{
	    		$("#NetAmount").val(parseFloat(total_net_amount));	
	    	}else{
		    	var net_amount = (parseFloat(add_charge)+parseFloat(total_net_amount));
		    	// console.log(net_amount)
		     	$("#NetAmount").val(net_amount.toFixed(2));
	    }

	    
	    $("#SubAmount").val(total_amount.toFixed(2));	
	    $("#Cgst").val(total_cgst.toFixed(2));	
	    $("#Sgst").val(total_sgst.toFixed(2));	
	    $("#Igst").val(total_igst.toFixed(2));	
	    $("#TotalGst").val(total_gst.toFixed(2));	
	}

    /////// Submit Form Data
    $("#MainForm").unbind('submit').on('submit', function() {

    	var formData = new FormData(this);
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
	
    

</script>