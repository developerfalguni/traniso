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
						<?php echo form_dropdown('invoice_list', [], '', 'class="form-control form-control-sm mb-0" id="exportInvoiceSelect2" '); ?>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group row">
					<label class="col-sm-3 col-form-label col-form-label-sm">Job No : </label>
					<div class="col-sm-9" id="JobRemove">
						<?php echo form_dropdown('job_list', [], '', 'class="form-control form-control-sm mb-0" id="jobSelect2" '); ?>
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
						<h3 class="bold mb-0">Invoice for <?php echo $type ?> - Non GST Billing</h3>
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
					                    <th class="text-center align-middle">##</th>
					                    <th class="align-middle">Charge Head</th>
					                    <th class="align-middle">Hsn</th>
					                    <th class="align-middle text-center">Currancy</th>
					                    <th class="align-middle text-center">EX Rate</th>
					                    <th class="align-middle text-center">Rate</th>
					                    <th class="align-middle text-center">INR Rate</th>
					                    <th class="align-middle text-center">Unit</th>
					                    <th class="align-middle text-center">Qty</th>
					                    <th class="align-middle text-center">Total</th>
					                    <th class="aligncenter"><a class="CheckAll"><i class="icon-trashcan"></i></a></th>
					               </tr>
				            	</thead>
				            	<tbody>
					            	<?php 
					            	$sr_no = 1;
					            	?>
					            	<tr class="TemplateRow">

					            		<td class="align-middle text-center grayLight SortHandle">
					            			<i class="icon-bars"></i>
					            			<input type="hidden" value="" name="kbr_invoice_item_id[]">
					            			<input type="hidden" value="" name="kbr_job_item_id[]">
					            		</td>
										<td class="align-middle"><input type="text" class="form-control form-control-sm Numeric Validate text-center SrNo" name="kbr_sr_no[]" value="<?php echo $sr_no ?>"></td>
										<td class="align-middle">
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
											<?php echo form_dropdown('kbr_unit[]', getSelectOptions('units'), '', 'class="Unchanged form-control form-control-sm Unit"'); ?>
										</td>

										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric Units Validate" name="kbr_qty[]" value="" onkeypress="return isNumberDot(event)" required>
										</td>

										<td class="align-middle">
											<input type="text" class="form-control form-control-sm Numeric Amount Validate" name="kbr_amount[]" readonly value="" required>
										</td>

										<td><button type="button" class="btn btn-danger btn-sm DelButton" disabled><i class="icon-minus fa fa-minus"></i></button></td>
									</tr>
									<?php $sr_no++; ?>					
								</tbody>
							</table>
						
							<button type="submit" class="btn btn-success AddButton m-2"><i class="icon-white icon-plus"></i> New Item</button>
						</div>

						
						<div class="row m-0 mt-2">
							<div class="col-lg-2 border-right border-top border-bottom">
								<span class="small bold">Additional charges</span>
							</div>
							<div class="col-lg-4 border-right border-bottom border-top p-1">
								<input type="text" class="form-control form-control-sm Charge" name="additional_charge" id="AdditionalCharge" value="" onkeypress="return isNumber(event)">
							</div>
							<div class="col-lg-2 border-right border-bottom border-top">
								<span class="small bold">Round Off</span>
							</div>
							<div class="col-lg-4 border-right border-bottom border-top p-1">
								<input type="text" class="form-control form-control-sm" id="RoundOff" name="roundoff" value="" onkeypress="return isNumber(event)">
							</div>
						</div>
						<div class="row m-0">
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
		<div class="card-body p-2 pt-0">
			
		</div>
		
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-warning" id="Print"><i class="fa fa-print"></i> Print</button>
					<button type="button" class="btn btn-success" id="Add" onclick="updateInvoice('0')"><i class="fa fa-plus"></i> Add</button>
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
		$('#billingParty').kaabar_autocomplete({source: '<?php echo site_url('master/operation/party/ajax2') ?>', alertText: 'Billing Party'});
	});
	// Shorthand for $( document ).ready()
	$(function() {
	    updateInvoice('0');
	});

	$(document).ready(function() {

		$('#exportInvoiceSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/invoiceList/',
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
	    	var invoice_id = $(this).val();
	    	updateInvoice(invoice_id);
	    });

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
	    	var job_id = $(this).val();
	    	$('#MainForm')[0].reset();
	    	updateJob(job_id);
	    });
	});

	function updateInvoice(id = null)
    {   
    	if(! id){
        	return false;
        }
        
        if(id){
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
                    if(response.success == false){
                    	$("#loader").hide();
   						$('#MainForm')[0].reset();
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

	            		$('#JobRemove').html('');
	            		
	            		if(response.invoice.jobNo)
	            			$('#JobRemove').html('<span>'+response.invoice.jobNo+'</span>');
	            		else
	            			$('#JobRemove').html('<span>N/A</span>');

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
                	}

					if(my_html){
                        $('.TemplateRow:last-child').before(my_html);
                        getTotal();
                        $('.TemplateRow:last-child').find('.SrNo').val(parseInt(sr_no)+parseInt(1));
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

					$('#updateInvoice').on('click', 'button.AddButton', function(e) {

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

					// Data Entry +/-
					var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';

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

				    ///////// Autocomplete Bill Item
					$('.BillItemCode').cskaabar_autocomplete({source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>'});
					$('.CurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.IsINR'});

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

					$('#MainForm').on('keyup', '.Charge', function() {
						getTotal();
					});

					function getTotal(){
						var total_amount = 0;
					    $(".Amount").each(function(){
					        total_amount += +$(this).val();

					        var add_charge = $("#AdditionalCharge").val();
						    if(!add_charge)
					    	{
					    		$("#NetAmount").val(total_amount.toFixed(2));	
					    	}else{
						    	var net_amount = (parseFloat(add_charge) + parseFloat(total_amount));
						    	$("#NetAmount").val(net_amount.toFixed(2));
						    }
					    });
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

					$("#Print").on('click', function(e){
						e.preventDefault();
						var id = $("input[name=id]").val();
						if(id > 0){
							let a = document.createElement('a');
							a.target = '_blank';
							a.href = "<?php echo base_url($this->_clspath.$this->_class.'/print/');?>"+id;
							a.click();
						}else{
							Swal.fire({
								html: "please select any Invoice for print...!",
								icon: "error",
							})
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
            $("#modal-containers").modal('hide');
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

     function updateJob(id = null)
    {   
    	if(! id){
        	return false;
        }
        
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
            $("#modal-containers").modal('hide');
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

</script>