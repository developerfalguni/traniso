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
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=0.5, user-scalable=yes, minimal-ui">
	<title><?php echo isset($page_title) ? humanize($page_title) : (isset($page) ? humanize($page) : "Untitled") ?></title>
	<meta name="description" content="Welcome to KAABAR Shipping Solutions" />
	<meta name="author" content="Chetan Patel" />

	<?php if (isset($auto_refresh)) { echo '<meta http-equiv="refresh" content="' . $auto_refresh . '" />'; } ?>


	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="/assets/plugins/fontawesome-free/css/all.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Tempusdominus Bootstrap 4 -->
	<link rel="stylesheet" href="/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<!-- Select2 -->
  	<link rel="stylesheet" href="/assets/plugins/select2/css/select2.min.css">
  	<link rel="stylesheet" href="/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	<!-- iCheck -->
	<link rel="stylesheet" href="/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<!-- JQVMap -->
	<link rel="stylesheet" href="/assets/plugins/jqvmap/jqvmap.min.css">
	<!-- daterange picker -->
  	<link rel="stylesheet" href="/assets/plugins/daterangepicker/daterangepicker.css">
  	<!-- iCheck for checkboxes and radio inputs -->
  	<link rel="stylesheet" href="/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  	<!-- Filepond for Upload Files -->
  	<link rel="stylesheet" type="text/css" href="/assets/plugins/filepond/css/filepond.css">
	<link rel="stylesheet" type="text/css" href="/assets/plugins/filepond/css/filepond-plugin-image-preview.css">
	
	<!-- Sweet Alert -->
	<link rel="stylesheet" type="text/css" href="/assets/vendors/sweetalert2/sweetalert2.min.css">
	
	<!-- Vendor Script -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/pnotify/pnotify.min.css'); ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/jquery-ui/jquery-ui.css'); ?>" />
	
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/jquery-ui/jquery-ui.theme.min.css'); ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css'); ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/selectize/css/selectize.bootstrap3.css'); ?>" />
	
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/typeahead/typeahead.css'); ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/bootstrap-select/bootstrap-select.min.css'); ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/icomoon/style.css'); ?>" />

	

	<!-- Theme style -->
	<link rel="stylesheet" href="/assets/dist/css/adminlte.css">
	<!-- overlayScrollbars -->
	<link rel="stylesheet" href="/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
	<!-- Daterange picker -->
	<link rel="stylesheet" href="/assets/plugins/daterangepicker/daterangepicker.css">
	<!-- summernote -->
	<link rel="stylesheet" href="/assets/plugins/summernote/summernote-bs4.min.css">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/css/kaabar.css'); ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/fonts/Lato/font.css'); ?>" />

	<?php if (isset($stylesheet)) {
		foreach ($stylesheet as $css) {
			echo "<link rel=\"stylesheet\" href=".base_url('assets/vendors'.$css)." />\n\t";
		}
	} ?>


	<!-- jQuery -->
	<script src="/assets/plugins/jquery/jquery.min.js"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>
		$.widget.bridge('uibutton', $.ui.button)
	</script>
	<!-- Bootstrap 4 -->
	<script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- ChartJS -->
	<script src="/assets/plugins/chart.js/Chart.min.js"></script>
	<!-- Sparkline -->
	<script src="/assets/plugins/sparklines/sparkline.js"></script>
	<!-- JQVMap -->
	<script src="/assets/plugins/jqvmap/jquery.vmap.min.js"></script>
	<script src="/assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
	<!-- jQuery Knob Chart -->
	<script src="/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
	<!-- InputMask -->
	<script src="/assets/plugins/moment/moment.min.js"></script>
	<script src="/assets/plugins/inputmask/jquery.inputmask.min.js"></script>
	<!-- Tempusdominus Bootstrap 4 -->
	<script src="/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
	<!-- Select2 -->
	<script src="/assets/plugins/select2/js/select2.full.min.js"></script>
	<!-- Summernote -->
	<script src="/assets/plugins/summernote/summernote-bs4.min.js"></script>
	<!-- overlayScrollbars -->
	<script src="/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
	<!-- date-range-picker -->
	<script src="/assets/plugins/daterangepicker/daterangepicker.js"></script>
	<!-- Bootstrap Switch -->
	<script src="/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
	<!-- SweetAlert 2 -->
	<script src="/assets/vendors/sweetalert2/sweetalert2.min.js"></script>
	<!-- AdminLTE App -->
	<script src="/assets/dist/js/adminlte.js"></script>

	<script src="<?php echo base_url('assets/vendors/pnotify/pnotify.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/js/keypress-2.1.4.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/js/jquery.highlight-4.closure.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/jquery-ui/jquery-ui.min.js'); ?>"></script>
	
	<script src="<?php echo base_url('assets/vendors/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/selectize/js/selectize.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/backbonejs/underscore-min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/js/jquery.taconite.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/js/handlebars-v4.0.11.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/typeahead/typeahead.bundle.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendors/bootstrap-select/bootstrap-select.min.js'); ?>"></script>
	
	<script src="<?php echo base_url('assets/vendors/js/kaabar.js'); ?>"></script>
	
	<?php if (isset($javascript)) {
		foreach ($javascript as $js) {
			echo "<script src=".base_url('assets/'.$js)."></script>\n\t";
		}
	} ?>


	<!-- Babel polyfill, contains Promise -->
	<script src="/assets/plugins/filepond/js/browser-polyfill.min.js"></script>
    <!-- Get FilePond polyfills from the CDN -->
    <script src="/assets/plugins//filepond/js/filepond-polyfill.js"></script>
    <!-- Get FilePond JavaScript and its plugins from the CDN -->
    <script src="/assets/plugins/filepond/js/filepond.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-file-validate-size.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-image-preview.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-image-resize.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-image-crop.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-image-exif-orientation.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-image-transform.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-file-validate-size.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-file-encode.js"></script>
    <script src="/assets/plugins/filepond/js/filepond-plugin-file-validate-type.js"></script>

</head>

<body>

<!-- Content -->
<div class="container-fluid">
	<?php if (isset($page)) {
		$this->load->view($page);
	} ?>
</div> <!-- /container-fluid -->
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
});

</script>
</body>
</html>
