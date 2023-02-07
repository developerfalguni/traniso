<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
	</div>
	<div class="card-body p-2 pb-0">
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<?php echo form_dropdown('job_list', [0 =>'Select Job'], '', 'class="form-control mb-0" id="exportSelect2" '); ?>
				</div>
			</div>
			<div class="col-md-12" id="updateCostsheet">
			
			</div>
		</div>
	</div>
	<div class="card-body p-2 pt-0">
		
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {

		$('#exportSelect2').select2({
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
	    	$('#updateCostsheet').html('');
	    	updateCostsheet(job_id);
	    });
	});


    function updateCostsheet(id = null)
    {   
    	if(! id){
        	return false;
        }
        
        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>export/costsheet/get/'+id,
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
						my_html += '<form action="<?php echo base_url('export/costsheet/edit/') ?>'+id+'" method="post" id="frmUpdateCostsheet" enctype="multipart/form-data">';
							my_html += '<div class="modal-body p-0">';
								my_html += '<div class="table-responsive modalcustome">';
							        my_html += '<table class="table table-sm table-bordered DataEntry">';
							            my_html += '<thead>';
							            	my_html += '<tr>';
							                    my_html += '<th class="text-center align-middle width-50" rowspan="2"></th>';
							                    my_html += '<th class="text-center align-middle width-50" rowspan="2">##</th>';
							                    my_html += '<th class="align-middle width-200" rowspan="2">Charge Head</th>';
							                    my_html += '<th class="align-middle width-200" rowspan="2">Vendor Name</th>';
							                    my_html += '<th class="align-middle" rowspan="2">Billing Type</th>';
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
														
														my_html += '<td class="align-middle '+$non_stax+'">';
															my_html += item.billing_type;
														my_html += '</td>';
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
											my_html += '<td class="align-middle">';
												my_html += '<?php echo form_dropdown_single('kbr_billing_type[]', getEnumSetOptions('costsheets', 'billing_type'), 'TX', 'class="form-control form-control-sm Unchanged billingType"'); ?>';
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
								  	url: '<?php echo site_url('export') ?>/costsheet/deleteattach',
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
</script>