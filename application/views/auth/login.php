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
	<meta charset="utf-8">
	<meta name="description" content="User login page" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<meta name="author" content="Chetan Patel" />
	<title><?php echo isset($page_title) ? humanize($page_title) : (isset($page) ? humanize($page) : "Untitled") ?></title>

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
	<!-- icheck bootstrap -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	
	<!-- Custome style -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/pnotify/pnotify.min.css" />
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/icomoon/style.css" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/selectize/css/selectize.bootstrap3.css'); ?>" />
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/dist/css/adminlte.css">

	<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/fonts/Lato/font.css" />

	<!-- jQuery -->
	<script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo base_url() ?>assets/dist/js/adminlte.min.js"></script>
	
	<script src="<?php echo base_url('assets/vendors/selectize/js/selectize.min.js'); ?>"></script>
	<script src="<?php echo base_url() ?>assets/vendors/pnotify/pnotify.min.js"></script>
	<script src="<?php echo base_url() ?>assets/vendors/js/kaabar.js"></script>

	<!-- Fav and touch icons -->
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url() ?>assets/dist/img/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo base_url() ?>assets/dist/img/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url() ?>assets/dist/img/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url() ?>assets/dist/img/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url() ?>assets/dist/img/favicon/favicon-16x16.png">
	<link rel="manifest" href="<?php echo base_url() ?>assets/dist/img/favicon/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?php echo base_url() ?>assets/dist/img/favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

</head>
<body class="login-page">
	<div class="login-box">
		<!-- /.login-logo -->
		<div class="row">
			<div class="col-md-7"></div>
			<div class="col-md-4">
				<div class="card">
					<div class="card-header border-none text-center">
						<img src="<?php echo base_url("assets/dist/img/profile.png") ?>" width="100">	
					</div>
					<?php echo form_open($this->uri->uri_string()) ?>
						<div class="card-body login-card-body">
							<div class="form-group mb-3">
								<input type="username" name="username" class="form-control form-control-sm Focus <?php echo (strlen(form_error('username')) > 0 ? 'is-invalid' : '') ?>" placeholder="Enter Login id">
							</div>
							<div class="form-group mb-3">
								<input type="password" name="password" class="form-control form-control-sm <?php echo (strlen(form_error('password')) > 0 ? 'is-invalid' : '') ?>" placeholder="Enter Password">
							</div>
							<div class="form-group mb-3">
								<?php echo form_dropdown('login_finyear', $yearsList, $currfinYear, 'class="form-control form-control-sm" id="login_finyear"') ?>
							</div>
							<div class="row">
								<div class="col-6">
									<div class="icheck-primary">
										<input type="checkbox" id="remember" name="remember_me">
										<label for="remember">Remember Me</label>
									</div>
								</div>
								<div class="col-6 text-right">
									<a href="#">I forgot my password..?</a>
								</div>
							</div>
							
						</div>

						<div class="card-footer pt-0 mb-2">
							<button type="submit" class="btn btn-primary btn-block">Sign In</button>
						</div>

					</form>
				<!-- /.login-card-body -->
				</div>		
			</div>
			<div class="col-md-1"></div>
		</div>
		
	</div>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">

		var $login_finyear = $('#login_finyear').selectize();

		jQuery(function($) {

			$(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible');//hide others
				$(target).addClass('visible');//show target
			});

			$('#btn-login-dark').on('click', function(e) {
				$('body').attr('class', 'login-layout');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			});
			$('#btn-login-light').on('click', function(e) {
				$('body').attr('class', 'login-layout light-login');
				$('#id-text2').attr('class', 'grey');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			});
			$('#btn-login-blur').on('click', function(e) {
				$('body').attr('class', 'login-layout blur-login');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'light-blue');

				e.preventDefault();
			});

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
