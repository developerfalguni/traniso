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
                <div class="row">
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <h5 class="description-header">$35,210.43</h5>
                      <span class="description-text">TOTAL PENDING</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <span class="description-percentage text-warning"><i class="fa fa-caret-left"></i> 0%</span>
                      <h5 class="description-header">$10,390.90</h5>
                      <span class="description-text">TOTAL COST</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <span class="description-percentage text-success"><i class="fa fa-caret-up"></i> 20%</span>
                      <h5 class="description-header">$24,813.53</h5>
                      <span class="description-text">TOTAL PROFIT</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-3 col-6">
                    <div class="description-block">
                      <span class="description-percentage text-danger"><i class="fa fa-caret-down"></i> 18%</span>
                      <h5 class="description-header">1200</h5>
                      <span class="description-text">GOAL COMPLETIONS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                </div>
                <!-- /.row -->
              </div>
      		<div class="card-header">
        		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa-solid fa-layer-group"></i>') ?></span> <?php echo $page_title ?></h3>
        		<div class="card-tools">
          			<ol class="breadcrumb float-sm-right m-0">
              			<li class="breadcrumb-item"><a href="#">Home</a></li>
              			<li class="breadcrumb-item active mr-1">Dashboard v1</li>
            		</ol>
        		</div>
        	</div>
        	<div class="card-body">
        		<div class="row">
					<div class="col-md-9">
						<div id="container"></div>
					</div>
					<div class="col-md-3">
						<div class="form-group mb-0">
							<?php
								$default_company = $this->session->userdata("default_company");
								$company = $this->kaabar->getRow('companies', ['id' => $default_company['id']]);
								$finYear = $this->kaabar->getBackFinYear(date('Y-m-d', strtotime($company['establishment'])));
								$years   = explode('-', $finYear);
								$firstYear = $years[0];

								$currfinYear = $this->kaabar->getFinYear();
								$currfinYears   = explode('-', $currfinYear);

								for($year = $firstYear; $year <= date('Y'); $year++) {
									if($year == $currfinYears[1])
										break;
									$totalYears[$year.'_'.($year+1)] = $year.'-'.($year+1);		
								}
							?>
							<label class="control-label">Select Year</label>
							<?php echo form_dropdown('job_list', $totalYears, '', 'class="form-control" id="rptYears"'); ?>
						</div>
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

	function salespurchase(year = '<?php echo $default_company['financial_year'] ?>'){
		$.ajax({
	        type: "GET",
	        url: "<?php echo base_url($this->_clspath.$this->_class) ?>/getFinYearData/"+year,             
	        dataType: "json",
	        success: function(response){

	        	const category = response.sp_categories;
	        	const linedata = response.sp_series;

	        	sales_purchase = new Highcharts.chart('container', {
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
						categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
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
						data: [49.9, 71.5, 106.4, 49.9, 71.5, 106.4, 49.9, 71.5, 106.4, 49.9, 71.5, 106.4],
					}, {
						name: 'Purchase',
						data: [83.6, 78.8, 98.5, 71.5, 106.4, 49.9, 71.5, 106.4, 71.5, 106.4, 49.9, 71.5]
					}]
				});
	        }
	    });
	}

	$(document).ready(function() {
		salespurchase();
	});

</script>