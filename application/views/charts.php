<!DOCTYPE html>
<html lang="en">
<head>
	 <meta charset="utf-8">
	 <title><?php echo isset($page_title) ? humanize($page_title) : (isset($page) ? humanize($page) : "Untitled") ?></title>
	 <meta name="description" content="Welcome to KAABAR Shipping Solutions" />
	 <meta name="author" content="Chetan Patel" />
	 
	 <?php if (isset($auto_refresh)) { echo '<meta http-equiv="refresh" content="' . $auto_refresh . '" />'; } ?>
	 
	 <!-- Javascript -->
	 <script src="/assets/js/jquery-2.1.4.min.js"></script>
	 <script type="text/javascript">
$(function () {
	 var chart;
	 $(document).ready(function() {
		  chart = new Highcharts.Chart({
			   chart: {
					renderTo: 'container',
					type: 'line'
			   },
			   title: {
					text: '<?php echo $page_title ?>'
			   },
			   subtitle: {
					text: '<?php echo $subtitle ?>'
			   },
			   xAxis: {
					categories: ["<?php echo join('", "', $categories); ?>"],
					gridLineWidth: 1
			   },
			   yAxis: {
					title: {
						 text: '<?php echo $y_axis_title ?>'
					}
			   },
			   plotOptions: {
					line: {
						 dataLabels: {
							  enabled: true
						 },
						 enableMouseTracking: false
					}
			   },
			   series: [<?php foreach($series as $name => $s)
					echo '{name:"' . $name . '", data: [' . join(', ', $s) . ']},';
			   ?>]
		  });
	 });
});
	</script>
</head>

<body>
	 <script src="/assets/highcharts/code/highcharts.js"></script>
	 <!--<script src="//assets/highcharts/js/highcharts-themes/grid.js"></script>
	 <script src="//assets/highcharts/js/highcharts-themes/gray.js"></script>
	 <script src="//assets/highcharts/js/highcharts-themes/skies.js"></script>
	 <script src="//assets/highcharts/js/highcharts-themes/dark-blue.js"></script>
	 <script src="//assets/highcharts/js/highcharts-themes/dark-green.js"></script>-->
	 <div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
</body>
</html>
