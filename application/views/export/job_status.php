<div class="card card-primary">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			
		</div>
	</div>
	<div class="card-body">

			<div class="card card-default">
				<div class="card-header text-center">
					<h3 class="bold mb-0">Job Status</h3>
				</div>
				<div class="card-body p-0">
					<div class="row m-0">
						<div class="col-lg-6 border-right border-bottom">
							<div class="form-group mb-0 row p-2">
								<label class="col-sm-4 col-form-label col-form-label-sm h2">DETAILS OF JOBS : </label>
								<div class="col-sm-8">
									<?php echo form_dropdown('job_list', [0 =>'Select Job'], '', 'class="form-control form-control-sm mb-0" id="exportSelect2"'); ?>
								</div>
							</div>
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<div class="form-group mb-0 row p-2">
								<label class="col-form-label col-form-label-sm h2">Total No Of Containers</label>
							</div>
						</div>
						<div class="col-lg-4 border-bottom">
							<div class="form-group mb-0 row p-2">
								<span class="small pt-1" id="containers"></span>
							</div>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Status of Job</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small" id="Status"></span>
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Job No & Date</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small" id="jobNoDate"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">File Ref. No</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="FileRefNo"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Client</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="Client"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Shipper</span>
						</div>
						<div class="col-lg-3 border-bottom">
							<span class="small" id="Shipper"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Consignee</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="Consignee"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Notify</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="Notify"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Terms</span>
						</div>
						<div class="col-lg-3 border-bottom">
							<span class="small" id="Terms"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">POL</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="POL"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">POD</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="POD"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">FPOD</span>
						</div>
						<div class="col-lg-3 border-bottom">
							<span class="small" id="FPOD"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h6 class="bold p-2 mb-0">Invoice Details</h1> </div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-right border-bottom">
						<span class="small bold">Invoice No</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small" id="InvoiceNo"></span>
						</div>
						<div class="col-lg-2 border-right border-right border-bottom">
							<span class="small bold">SB No & Date</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small" id="sbNoDate"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h6 class="bold p-2 mb-0">Booking Details</h1> </div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Booking No</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small" id="BookingNoDate"></span>
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Liner</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small" id="Liner"></span>
						</div>
					</div>

					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">FRWD</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small" id="Frwd"></span>
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">CHA</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small" id="Cha"></span>
						</div>
					</div>

					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Container Size</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small bold">Container No</span>
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Truck No</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small bold">Transporter</span>
						</div>
					</div>
					<div id="ContainerList"></div>

					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h4 class="text-danger bold p-2 mb-0">Costsheet Details</h1> </div>
					</div>
					<div class="row m-0">
						<div class="col-lg-4 border-right border-bottom">
							<span class="small bold">Service</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small bold">Vendor Name</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small bold">Documents</span>
						</div>
					</div>
					<div id="CostsheetList"></div>

					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h6 class="bold p-2 mb-0">BL Details</h1> </div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right  border-right border-bottom">
							<span class="small bold">HBL No & Date</span>
						</div>
						<div class="col-lg-4 border-right  border-right border-bottom">
							<span class="small" id="HblNoDate"></span>
						</div>
						<div class="col-lg-2 border-right  border-right border-bottom">
							<span class="small bold">MBL No & Date</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small" id="MblNoDate"></span>
						</div>
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">HBL Type</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small" id="HblType"></span>
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">MBL Type</span>
						</div>
						<div class="col-lg-4 border-bottom">
							<span class="small" id="MblType"></span>
						</div>
					</div>

					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h6 class="bold p-2 mb-0">Billing Details</h1> </div>
					</div>
					<div class="row m-0">
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Debit Note</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="DebitNote"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Bill Date</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small" id="BillDate"></span>
						</div>
						<div class="col-lg-1 border-right border-bottom">
							<span class="small bold">Amount</span>
						</div>
						<div class="col-lg-3 border-bottom">
							<span class="small" id="Amount"></span>
						</div>
					</div>					

					<div class="row m-0">
						<div class="col-lg-12 border-bottom text-center"><h4 class="text-danger bold p-2 mb-0">Uploded Documents</h1> </div>
						
					</div>
					<div class="row m-0">
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Document Name</span>
						</div>
						<div class="col-lg-4 border-right border-bottom">
							<span class="small bold">No</span>
						</div>
						<div class="col-lg-2 border-right border-bottom">
							<span class="small bold">Date</span>
						</div>
						<div class="col-lg-3 border-right border-bottom">
							<span class="small bold">Remark</span>
						</div>
						<div class="col-lg-1 border-bottom">
							<span class="small bold">View</span>
						</div>
					</div>	
					<div id="UploadDocumentList"></div>				
				</div>
			</div>
		
	</div>
	<div class="card-footer">
		<div class="row">
			
		</div>
	</div> 
</div>



<script type="text/javascript">
	$(document).ready(function() {

		<?php if($job_id > 0){ ?>
			getJobDetails(<?php echo $job_id ?>);
		<?php } ?>
		
		$('#exportSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath) ?>job_status/jobsList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				// data: function (params) {
			 //  		return {
			 //    		searchTerm: params.term, // search term
			 //    		//page: params.page || 1
			 //  		};
			 //  	},
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
			createSearchChoice:function(term, data) {
				if ( $(data).filter( function() {
			  		return this.text.localeCompare(term)===0;
				}).length===0) {
			  		return {id:1, text:Chetan};
				}
			},
			//placeholder: 'Search for a repository',
	  		//minimumInputLength: 1,
	    }).on("change", function () {
	    	var job_id = $(this).val();
	    	getJobDetails(job_id);
		});

		$('.select2-selection__rendered').hover(function () {
		    $(this).removeAttr('title');
		});
		$(document).on('select2:open', () => {
	    	document.querySelector('.select2-search__field').focus();
	    });
	});


	function getJobDetails(job_id = null)
    {   
    	if(! job_id){
        	return false;
        }
        
        if(job_id){
        	$.ajax({
                url: '<?php echo site_url() ?>export/job_status/get/'+job_id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

					// console.log(response.jobs);           

                	/////// Job Details
                	$('#containers').text(response.jobs.cont);
                	$('#Status').html(response.jobs.status);
                	$('#jobNoDate').text(response.jobs.idkaabar_code+' / '+response.jobs.date);
                	$('#Client').text(response.jobs.party_name);
                	$('#Shipper').text(response.jobs.shipper_name);
                	$('#Consignee').text(response.jobs.consignee_name);
                	$('#Notify').text(response.jobs.notify_name);
                	$('#Terms').text(response.jobs.incoterms);
                	$('#POL').text(response.jobs.pol_name);
                	$('#POD').text(response.jobs.pod_name);
                	$('#FPOD').text(response.jobs.fpod_name);
                	
                	$('#InvoiceNo').text(response.jobs.invoice_no+ ' / ' + response.jobs.invoice_date);
                	$('#sbNoDate').text(response.jobs.sb_no+ ' / ' + response.jobs.sb_date);
                	$('#BookingNoDate').text(response.jobs.booking_no+' / '+response.jobs.booking_date);
                	$('#Liner').text(response.jobs.line_name);
                	$('#Frwd').text(response.jobs.forwarder_name);
                	$('#Cha').text(response.jobs.cha_name);
                	$('#BillDate').text(response.jobs.date);
                	$('#HblNoDate').text(response.jobs.hbl_no+' - '+response.jobs.hbl_date);
                	$('#HblType').text(response.jobs.hbl_type);
                	$('#MblNoDate').text(response.jobs.mbl_no+' - '+response.jobs.mbl_date);
                	$('#MblType').text(response.jobs.mbl_type);
                	$('#date').val(response.jobs.date);
                	var containerList = '';                	
     				if(response.containers.length){
				    	var result = JSON.parse(JSON.stringify(response.containers));
				    	for (var i = 0; i < result.length; i++) {
					        containerList += '<div class="row m-0">';
								containerList += '<div class="col-lg-2 border-right border-bottom">';
									containerList += '<span class="small">'+result[i].size+'</span>';
								containerList += '</div>';
								containerList += '<div class="col-lg-4 border-right border-bottom">';
									containerList += '<span class="small">'+result[i].number+'</span>';
								containerList += '</div>';
								containerList += '<div class="col-lg-2 border-right border-bottom">';
									containerList += '<span class="small">'+result[i].vehicle_no+'</span>';
								containerList += '</div>';
								containerList += '<div class="col-lg-4 border-bottom">';
									containerList += '<span class="small">'+result[i].transporter+'</span>';
								containerList += '</div>';
							containerList += '</div>';
						}
					}
					$('#ContainerList').html(containerList);

					var costsheetList = '';                	
     				if(response.costsheets.length){


     					console.log(response.costsheets)

				    	var result = JSON.parse(JSON.stringify(response.costsheets));
				    	for (var i = 0; i < result.length; i++) {
					        costsheetList += '<div class="row m-0">';
								costsheetList += '<div class="col-lg-4 border-right border-bottom">';
									costsheetList += '<span class="small">'+result[i].particulars+'</span>';
								costsheetList += '</div>';
								costsheetList += '<div class="col-lg-4 border-right border-bottom">';
									costsheetList += '<span class="small">'+result[i].vendor_name+'</span>';
								costsheetList += '</div>';
								costsheetList += '<div class="col-lg-4 border-bottom">';
									if(result[i].upfile != null)
										costsheetList += '<span class="small"><a href="'+result[i].upfile+'" download><i class="fa fa-download fa-lg text-success"></i></a></span>';
									else
										costsheetList += '<span class="small text-danger">No Document</span>';

								costsheetList += '</div>';
							costsheetList += '</div>';
						}
					}
					$('#CostsheetList').html(costsheetList);

					var uploadDocumentList = '';
					if(response.attach_documents.length){
				    	var result = JSON.parse(JSON.stringify(response.attach_documents));
				    	for (var i = 0; i < result.length; i++) {
					        uploadDocumentList += '<div class="row m-0">';
								uploadDocumentList += '<div class="col-lg-2 border-right border-bottom">';
									uploadDocumentList += '<span class="small">'+result[i].doc_name+'</span>';
								uploadDocumentList += '</div>';
								uploadDocumentList += '<div class="col-lg-4 border-right border-bottom">';
									uploadDocumentList += '<span class="small">'+result[i].vendor+'</span>';
								uploadDocumentList += '</div>';
								uploadDocumentList += '<div class="col-lg-2 border-right border-bottom">';
									uploadDocumentList += '<span class="small">'+result[i].created_on+'</span>';
								uploadDocumentList += '</div>';
								uploadDocumentList += '<div class="col-lg-3 border-right border-bottom">';
									uploadDocumentList += '<span class="small">'+result[i].doc_remark+'</span>';
								uploadDocumentList += '</div>';
								uploadDocumentList += '<div class="col-lg-1 border-bottom">';
									uploadDocumentList += '<span class="small"><a href="'+result[i].filename+'" download><i class="fa fa-download fa-lg text-success"></i></a></span>';
								uploadDocumentList += '</div>';
							uploadDocumentList += '</div>';
						}
					}
					$('#UploadDocumentList').html(uploadDocumentList);

					$("#loader").hide();

					return false;
                	
                	
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