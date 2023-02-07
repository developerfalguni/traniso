<style type="text/css">
	.highcharts-figure,
	.highcharts-data-table table {
	  margin: 1em auto;
	}

	#container {
	  height: 400px;
	}

	.highcharts-data-table table {
	  font-family: Verdana, sans-serif;
	  border-collapse: collapse;
	  border: 1px solid #ebebeb;
	  margin: 10px auto;
	  text-align: center;
	  width: 100%;
	  max-width: 500px;
	}

	.highcharts-data-table caption {
	  padding: 1em 0;
	  font-size: 1.2em;
	  color: #555;
	}

	.highcharts-data-table th {
	  font-weight: 600;
	  padding: 0.5em;
	}

	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
	  padding: 0.5em;
	}

	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
	  background: #f8f8f8;
	}

	.highcharts-data-table tr:hover {
	  background: #f1f7ff;
	}
</style>

<div class="row pt-1">
	<div class="col-sm-12 p-0">	
		<div class="card">
			<div class="card-header">
        		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-solid fa-layer-group"></i> '.$page_title) ?></span></h3>
        		<div class="card-tools">
          			<ol class="breadcrumb float-sm-right m-0">
              			<?php 
							echo form_dropdown('job_list', $this->_yearsList, $this->_default_company['financial_year'], 'class="form-control select2" id="rptYears"'); 
						?>
              		</ol>
        		</div>
        	</div>
        	<div class="card-header">
        		<div class="row">
					<div class="col-lg-3 col-6">
						<div class="small-box bg-info">
							<div class="inner">
								<h3><?php echo $export ?></h3>
								<p>Total Export Jobs</p>
							</div>
							<div class="icon">
								<i class="fa fa-upload"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-6">
						<div class="small-box bg-info">
							<div class="inner">
								<h3><?php echo $import ?></h3>
								<p>Total Import Jobs</p>
							</div>
							<div class="icon">
								<i class="fa fa-download"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-6">
						<div class="small-box bg-danger">
							<div class="inner">
								<h3><?php echo $invoice ?></h3>
								<p>Pending Billing</p>
							</div>
							<div class="icon">
								<i class="fa fa-file-invoice"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-6">
						<div class="small-box bg-danger">
							<div class="inner">
								<h3><?php echo $einvoice ?></h3>
								<p>Pending eInvoice</p>
							</div>
							<div class="icon">
								<i class="fa fa-file-invoice"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
      		<div class="card-body">
        		<div class="row">
					<div class="col-md-12">
						<div id="costsheet-container"></div>
					</div>
				</div>
        	</div>
      	</div>
	</div>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script type="text/javascript">

	$('#rptYears').select2().on("change", function () {
    	salespurchase($(this).val())
    });


	function salespurchase(year = '<?php echo $this->_default_company['financial_year'] ?>'){

		$.ajax({
	        type: "GET",
	        url: "<?php echo base_url($this->_clspath.$this->_class) ?>/getFinYearData/"+year,             
	        dataType: "json",
	        success: function(response){

	        	const category = response.sp_categories;
	        	var linedata = response.sp_series;

	        	sales_purchase = new Highcharts.chart('costsheet-container', {
					chart: {
						type: 'column'
					},
					title: {
						text: response.sp_page_title
					},
					subtitle: {
						text: response.sp_sub_title
					},
					// colors: [
					//         '#ff0000',
					//         '#00ff00',
					//       ],
					xAxis: {
						title: {
							 text: 'Months'
						},
						categories: response.sp_categories,
						gridLineWidth: 1,
						crosshair: true,
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Amount in INR'
						}
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f} INR</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Sales',
						data: linedata.Sales,
					}, {
						name: 'Purchase',
						data: linedata.Purchase
					}, {
						name: 'Billing',
						data: linedata.Billing
					}]
				});
	        }
	    });
	}

	$(document).ready(function() {
		salespurchase();
	});

</script>