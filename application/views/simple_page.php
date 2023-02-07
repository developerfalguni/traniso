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
	<script src="/js/jquery-1.8.3.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>
	<script src="/js/bootstrap-notify.js"></script>
	<?php if (isset($javascript)) {
		foreach ($javascript as $js) {
			echo "<script src=\"/assets/$js\"></script>\n\t";
		}
	} ?>

	<!-- Styles -->
	<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/assets/css/idex.css" />
	<link rel="stylesheet" href="/css/bootstrap-notify.css" />
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
<div class="container-fluid">
	<div class="row">
	   <div class="col-md-12">
		<?php if (isset($page)) echo $page; ?>
	   </div>
   </div>

</div> <!-- /container-fluid -->
<!-- /Content -->

<script>
$('body').tooltip({selector: "[rel=tooltip]"});

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
			else 
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
		$this->session->unset_userdata('alert');
	}
?>

</script>
</body>
</html>
