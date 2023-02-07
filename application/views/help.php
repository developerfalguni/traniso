<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="/favicon.ico">

	<title>Help</title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/help.css') ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/fonts/Lato/font.css') ?>" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>

	<!-- Fixed navbar -->
	<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Documentation</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<!-- <ul class="nav navbar-nav">
					<li class="active"><a href="#">Home</a></li>
				</ul> -->
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#"><i class="glyphicon glyphicon-question-sign"></i></a></li>
					<li><a href="#"><i class="glyphicon glyphicon-print"></i></a></li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3">
				<div class="well">
					<ul>
					<?php echo $nav; ?>
					</ul>
				</div>
			</div>

			<div class="col-md-9">
				<h1><?php echo $title ?></h1>
				<?php echo $content ?>
			</div>
		</div>
	</div>

	<script src="<?php echo base_url('assets/js/jquery-2.1.4.min.js') ?>"></script>
	<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
</body>
</html>
