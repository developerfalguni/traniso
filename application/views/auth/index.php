<?php
// Header important to disable Browser Back / Forward button after Logged out.
$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
$this->output->set_header('Pragma: no-cache');
$this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
	<title><?php echo isset($page_title) ? humanize($page_title) : (isset($page) ? humanize($page) : "Untitled") ?></title>
	<meta name="description" content="Welcome to KAABAR Shipping Solutions" />
	<meta name="author" content="Chetan Patel" />
	
	<!-- HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Resource -->
	<?php if (isset($resource)) {
		foreach ($resource as $res) {
			echo "<link rel=\"resource\" type=\"application/l10n\" href=\"" . base_url($res) . "\" />\n\t";
		}
	} ?>

	<!-- Javascript -->
	<!-- jQuery -->
	<script src="/assets/plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- AdminLTE App -->
	<script src="/assets/dist/js/adminlte.min.js"></script>
	
	<script src="/assets/vendors/pnotify/pnotify.min.js"></script>
	<script src="/assets/vendors/js/kaabar.js"></script>

	<?php if (isset($javascript)) {
		foreach ($javascript as $js) {
			echo "<script src=\"/assets/$js\"></script>\n\t";
		}
	} ?>

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="/assets/plugins/fontawesome-free/css/all.min.css">
	<!-- icheck bootstrap -->
	<link rel="stylesheet" href="/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	
	<!-- Custome style -->
	<link rel="stylesheet" href="/assets/vendors/pnotify/pnotify.min.css" />
	<link rel="stylesheet" href="/assets/vendors/icomoon/style.css" />
	<!-- Theme style -->
	<link rel="stylesheet" href="/assets/dist/css/adminlte.min.css">

	<link rel="stylesheet" href="/assets/vendors/fonts/Lato/font.css" />

	<!--[if lte IE 7]>
		<script src="<script src="/assets/icomoon/lte-ie7.js"></script>
	<![endif]-->

	<?php if (isset($stylesheet)) {
		foreach ($stylesheet as $css) {
			echo "<link rel=\"stylesheet\" href=\"/assets/$css\" />\n\t";
		}
	} ?>

	<!-- Fav and touch icons -->
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
</head>

<body>
<!-- Content -->
<div class="container">
<?php 
	if (isset($page))
		$this->load->view($page);

	if (isset($pages) && is_array($pages)) {
		foreach ($pages as $page) {
			$this->load->view($page);
		}
	}
?>
</div> <!-- /container -->
<!-- /Content -->

<script>
$(document).ready(function() {
<?php
	$alert = $this->session->userdata('alert');
	if($alert) {
		foreach($alert as $class => $message) {
			if (is_array($message)) {
				echo "
					new PNotify({
						title: '" . ucfirst($class) . "',
						text: '<i class=\"icon-angle-double-right\"></i> " . implode('<br /><i class=\"icon-angle-double-right\"></i> ', $message) . "',
						type: '$class',
						nonblock: {
							nonblock: true,
							nonblock_opacity: .2
						}
					});\n";
			}
			else{
				echo "
					new PNotify({
						title: '" . ucfirst($class) . "',
						text: '<i class=\"icon-angle-double-right\"></i> " . str_replace("\n", '<br /><i class=\"icon-angle-double-right\"></i> ', $message) . "',
						type: '$class',
						nonblock: {
							nonblock: true,
							nonblock_opacity: .2
						}
					});\n";
			}
		}
		$this->session->unset_userdata('alert');
	}
?>
});

</script>
</body>
</html>