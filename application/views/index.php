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

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/font-awesome/css/font-awesome.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Tempusdominus Bootstrap 4 -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<!-- Select2 -->
  	<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/select2-develop/dist/css/select2.css">
  	<!-- <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css"> -->
	<!-- iCheck -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<!-- JQVMap -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/jqvmap/jqvmap.min.css">
	<!-- daterange picker -->
  	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/daterangepicker/daterangepicker.css">
  	<!-- iCheck for checkboxes and radio inputs -->
  	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  	<!-- Filepond for Upload Files -->
  	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/filepond/css/filepond.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/filepond/css/filepond-plugin-image-preview.css">
	
	<!-- Sweet Alert -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.css">
	
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
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/dist/css/adminlte.css">
	<!-- overlayScrollbars -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
	<!-- Daterange picker -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/daterangepicker/daterangepicker.css">
	<!-- summernote -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/summernote/summernote-bs4.min.css">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/css/kaabar.css'); ?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/vendors/fonts/Lato/font.css'); ?>" />

	<?php if (isset($stylesheet)) {
		foreach ($stylesheet as $css) {
			echo "<link rel=\"stylesheet\" href=".base_url('assets/vendors'.$css)." />\n\t";
		}
	} ?>


	<!-- jQuery -->
	<script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="<?php echo base_url() ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>
		$.widget.bridge('uibutton', $.ui.button)
	</script>
	<!-- Bootstrap 4 -->
	<script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- ChartJS -->
	<script src="<?php echo base_url() ?>assets/plugins/chart.js/Chart.min.js"></script>
	<!-- Sparkline -->
	<script src="<?php echo base_url() ?>assets/plugins/sparklines/sparkline.js"></script>
	<!-- JQVMap -->
	<script src="<?php echo base_url() ?>assets/plugins/jqvmap/jquery.vmap.min.js"></script>
	<script src="<?php echo base_url() ?>assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
	<!-- jQuery Knob Chart -->
	<script src="<?php echo base_url() ?>assets/plugins/jquery-knob/jquery.knob.min.js"></script>
	<!-- InputMask -->
	<script src="<?php echo base_url() ?>assets/plugins/moment/moment.min.js"></script>
	<script src="<?php echo base_url() ?>assets/plugins/inputmask/jquery.inputmask.min.js"></script>
	<!-- Tempusdominus Bootstrap 4 -->
	<script src="<?php echo base_url() ?>assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
	<!-- Select2 -->
	<script src="<?php echo base_url() ?>assets/vendors/select2-develop/dist/js/select2.min.js"></script>
	<!-- Summernote -->
	<script src="<?php echo base_url() ?>assets/plugins/summernote/summernote-bs4.min.js"></script>
	<!-- overlayScrollbars -->
	<script src="<?php echo base_url() ?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
	<!-- date-range-picker -->
	<script src="<?php echo base_url() ?>assets/plugins/daterangepicker/daterangepicker.js"></script>
	<!-- Bootstrap Switch -->
	<script src="<?php echo base_url() ?>assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
	<!-- SweetAlert 2 -->
	<script src="<?php echo base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo base_url() ?>assets/dist/js/adminlte.js"></script>

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
	<script src="<?php echo base_url() ?>assets/plugins/filepond/js/browser-polyfill.min.js"></script>
    <!-- Get FilePond polyfills from the CDN -->
    <script src="<?php echo base_url() ?>assets/plugins//filepond/js/filepond-polyfill.js"></script>
    <!-- Get FilePond JavaScript and its plugins from the CDN -->
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-file-validate-size.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-image-preview.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-image-resize.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-image-crop.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-image-exif-orientation.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-image-transform.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-file-validate-size.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-file-encode.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/filepond/js/filepond-plugin-file-validate-type.js"></script>


    <!-- END: Page JS-->
    
	<!-- JAVASCRIPT ENDED -->	
	<style>
		/* Paste this css to your style sheet file or under head tag */
		/* This only works with JavaScript,
		if it's not present, don't show loader */
	
		.error {color: #FF0000;}

		.no-js #loader {
			display: none;
		}

		.js #loader {
			display: block;
			position: absolute;
			left: 100px;
			top: 0;
		}

		.se-pre-con {
			position: fixed;
			left: 0px;
			top: 0px;
			width: 100%;
			height: 100%;
			z-index: 9999;
			background: url("<?php echo base_url() ?>assets/dist/img/pageloader/loader-64x/Preloader_3.gif") center no-repeat;
			background-color: #FFF;
			opacity: 0.7;
		}
		.uppercase {
				text-transform: uppercase;
		}
	</style>
	<script>
		//paste this code under head tag or in a seperate js file.
		// Wait for window load
		$(window).on('load', function(){ 
			// Animate loader off screen
			$(".se-pre-con").fadeOut("slow");
		});

		$("#loader").ajaxStart(function(){
			$(this).show();
		});

		$("#loader").ajaxComplete(function(){
		   	$(this).hide();
		});

	</script>

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

	<?php
		$default_company = $this->_default_company;
		$perm = Auth::get('permissions');
		$companies = $this->kaabar->getRows('companies');
	?>

	<div class="wrapper">
		<!-- Preloader -->
		<div class="se-pre-con" id="loader"></div>
		<!-- Navbar -->

		<nav class="main-header navbar navbar-expand navbar-white navbar-light">

			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
				</li>
			</ul>

			<!-- Right navbar links -->
			<ul class="navbar-nav ml-auto">
				<!-- Navbar Search -->
				<li class="nav-item">
			    	<a class="nav-link" data-widget="navbar-search" href="#" role="button">
			      		<i class="fa fa-search"></i>
			    	</a>
			    	<div class="navbar-search-block">
				      	<form class="form-inline">
				        	<div class="input-group input-group-sm">
				          		<input class="form-control form-control-sm form-control-navbar" type="search" placeholder="Search" aria-label="Search">
			          			<div class="input-group-append">
				            		<button class="btn btn-navbar" type="submit">
				              			<i class="fa fa-search"></i>
				            		</button>
				            		<button class="btn btn-navbar" type="button" data-widget="navbar-search">
				              			<i class="fa fa-times"></i>
				            		</button>
				          		</div>
				        	</div>
				      	</form>
				    </div>
				 </li>

				<!-- Messages Dropdown Menu -->
				<li class="nav-item dropdown">
					<a class="nav-link text-info" data-toggle="dropdown" href="#">
			      		<?php echo $default_company['name'].' - '.$default_company['code']; ?><i class="pl-1 fa fa-angle-down"></i>
			      	</a>
			      	<div class="dropdown-menu dropdown-menu-right">
						<?php
							foreach ($companies as $c) {
								if (Auth::isAdmin() OR isset($perm[$c['id']]))
									echo '<span class="dropdown-item"><i class="fa fa-angle-right"></i> ' . anchor('main/default_company/'.$c['id'], $c['code'] . ' - ' . $c['name'], 'class="black"') . '</span>';
							}
						?>
					</div>
				</li>
			  	<!-- Notifications Dropdown Menu -->
			  	<li class="nav-item dropdown">
			    	<a class="nav-link text-info" data-toggle="dropdown" href="#">
			      		<?php echo str_replace('_', '-', $default_company['financial_year']) ?><i class="pl-1 fa fa-angle-down"></i>
			      	</a>
			      	<div class="dropdown-menu dropdown-menu-right">
			      		<?php

			      			//echo form_dropdown('job_list', $this->_yearsList, $this->_currfinYear, 'class="form-control select2" id="rptYears"'); 

			      			foreach ($this->_yearsList as $years => $year) {
			      				$y = explode('_', $year);
			      				echo '<span class="dropdown-item"><i class="fa fa-angle-right"></i> ' . anchor('main/default_company/'.$default_company['id'].'/'.$y[0].'_'.$y[1], $year, 'class="black"') . '</span>';
			      			}

							
						?>
					</div>
			  	</li>
			  	<!-- Notifications Dropdown Menu -->
			  	<li class="nav-item dropdown">
			    	<a class="nav-link text-info" data-toggle="dropdown" href="#">
			    		<i class="fa fa-users"></i> <i class="pl-1 fa fa-angle-down"></i>
			      	</a>
			      	<div class="dropdown-menu dropdown-menu-right">
			      		<span class="dropdown-item"><?php echo anchor('auth/newuser', '<i class="fa fa-user-plus"></i> New Users', 'class="black"') ?></span>
						<span class="dropdown-item"><?php echo anchor('auth/permission', '<i class="fa fa-lock"></i> Permissions', 'class="black"') ?></span>
						<span class="dropdown-item"><?php echo anchor('auth/user', '<i class="fa fa-users"></i> Users', 'class="black"') ?></span>
			      	</div>
			  	</li>
			  	<!-- Notifications Dropdown Menu -->
			  	<li class="nav-item dropdown">
			    	<a class="nav-link user-panel" data-toggle="dropdown" href="#">
			      		<div class="image">
				      		<img src="<?php echo base_url() ?>assets/dist/img/profile.png" class="img-circle elevation-2" alt="User Image"> <span class="pl-1 user-info"><small>Welcome,</small> <?php echo Auth::get('username') ?></span>
				    	</div>
				    </a>
			      	<div class="dropdown-menu dropdown-menu-right">
			      		<?php echo anchor('main/settings', '<i class="icon-cog"></i> Settings', 'class="dropdown-item"'); ?>
						<?php if (Auth::isAdmin()) :
			      			echo anchor('main/global_settings', '<i class="feather icon-layers"></i> Global Settings', 'class="dropdown-item"');
			      		endif; ?>
			      		<?php echo anchor('auth/user/change_password', '<i class="icon-key"></i> Change Password', 'class="dropdown-item"'); ?>
						
						<div class="dropdown-divider"></div>
						<?php echo anchor('main/logout', '<i class="icon-sign-out"></i> Logout', 'class="dropdown-item"'); ?>
			      	</div>
			  	</li>
			  	<li class="nav-item">
			    	<a class="nav-link" data-widget="fullscreen" href="#" role="button">
			      		<i class="fa fa-expand-arrows-alt"></i>
			    	</a>
			  	</li>
			  	
			</ul>
		</nav>
		<!-- /.navbar -->
		<!-- Main Sidebar Container -->
		<aside class="main-sidebar sidebar-dark-primary elevation-4">

			<!-- Brand Logo -->
			<a href="<?php echo base_url() ?>" class="brand-link">
			  <img src="<?php echo base_url() ?>assets/dist/img/logo-light.png" alt="CHA Software" class="brand-image img-circle elevation-3" style="opacity: .8">
			  <span class="brand-text font-weight">myTraniso</span>
			</a>

			<!-- Sidebar -->
			<div class="sidebar">
			  	<!-- Sidebar user panel (optional) -->
			  	<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			    	<div class="image">
			      		<img src="<?php echo base_url() ?>assets/dist/img/profile.png" class="img-circle elevation-2" alt="User Image">
			    	</div>
			    	<div class="info">
			      		<a href="#" class="d-block"><?php echo humanize(Auth::get('username')); ?></a>
			    	</div>
			  	</div>

			  	<!-- SidebarSearch Form -->
			  	<div class="form-inline">
			    	<form method="POST" action="#" target="_blank" id="ajaxMenuLink">
						<input class="form-control form-control-sm form-control-sidebar" type="text" placeholder="Where you want to go...?" id="ajaxMenu" aria-label="Search">
					</form>
			    </div>

			  	<!-- Sidebar Menu -->
			  	<nav class="mt-2">

			    	<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
			      	<!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
			      		<?php
							if (isset($perm[$default_company['id']]))
								$perm = $perm[$default_company['id']];
								$menu = $this->config->item('menus');
								$submenu = function($submenus, $permission = 0, $parent = false) use (&$submenu, &$perm) {
								$buffer = false;
								foreach ($submenus as $menu => $items) {

									if (isset($items['hide'])) 
										continue;

									$li = false;
									if (Auth::isAdmin()) {
										$buffer .= '<li class="nav-item">' . $items['link'];
										$li = true;
									}
									else if (isset($perm[$menu]) AND !isset($items['url'])) {
										$buffer .= '<li class="nav-item">' . $items['link'];
										$li = true;
									}
									else if ($permission | isset($perm[$menu])) {
										$buffer .= '<li class="nav-item">' . $items['link'];
										$li = true;
									}

									if (isset($items['nodes'])) {
										$ul = $submenu($items['nodes'], ($permission | (isset($perm[$menu]) ? $perm[$menu] : 0)), $menu);
										
										if ($ul) 
											$buffer .= "\n<ul class=\"nav nav-treeview\">\n$ul</ul>\n";
									}
									if ($li)
										$buffer .= "</li>\n";
								}
								return $buffer;
							};
							echo $submenu($menu);
						?>

			      		<li class="nav-header">LABELS</li>
			      		<li class="nav-item">
			        		<a href="#" class="nav-link">
			          			<i class="nav-icon far fa-circle text-danger"></i>
			          			<p class="text">Important</p>
			        		</a>
			      		</li>
			      		<li class="nav-item">
			        		<a href="#" class="nav-link">
			          			<i class="nav-icon far fa-circle text-warning"></i>
			          			<p>Warning</p>
			        		</a>
			      		</li>
			      		<li class="nav-item">
			        		<a href="#" class="nav-link">
			          			<i class="nav-icon far fa-circle text-info"></i>
			          			<p>Informational</p>
			        		</a>
			      		</li>
			    	</ul>
			  	</nav>
			  	<!-- /.sidebar-menu -->
			</div>
			<!-- /.sidebar -->
		</aside>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- /.content-header -->
		    <section class="content">
		    	<div class="container-fluid">
	        		<!-- PAGE CONTENT BEGINS -->
	        		<div class="row pt-1">
						<div class="col-sm-12 p-0">	
							<?php if (isset($page)) $this->load->view($page); ?>
						</div>
					</div>
					<!-- PAGE CONTENT ENDS -->
					
				</div>
			</section>
		<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
		<footer class="main-footer">
			<strong>Copyright &copy; 2016-<?php echo date('Y') ?> <a href="https://www.connectithub.com">Connect IT Hub</a>.</strong>
			All rights reserved.
			<div class="float-right d-none d-sm-inline-block">
	  			<b>Version</b> 3.2.0
			</div>
		</footer>

	</div>
	<!-- ./wrapper -->
	<script type="text/javascript">
		
		$(document).ready(function() {

			if (jQuery.isFunction(jQuery.fn.highlight)) {
				<?php
				if (isset($search) && ! is_array($search) && strlen(trim($search)) > 0) {
					echo "$('table.table-striped tbody').highlight('" . strtoupper($search) . "');";
				}
				else if (isset($search) && is_array($search)) {
					foreach ($search as $s) {
						if (strlen(trim($s)) > 0)
							echo "$('table.table-striped tbody').highlight('" . strtoupper($s) . "');\n";
					}
				}
				?>
			}

			var menu = new Bloodhound({
				limit: 50,
				prefetch: '<?php echo site_url('main/ajaxMenu') ?>',
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,
			});
			
			<?php if ($this->_class == 'main') echo 'menu.clearPrefetchCache();'; ?>

			menu.initialize();
			$('#ajaxMenu').typeahead({
				hint: false,
				highlight: true,
				minLength: 1
			}, {
				name: 'tt_menu_search',
				displayKey: 'name',
				source: menu.ttAdapter(),
				templates: {
					suggestion: Handlebars.compile('<p><span class="grayDark">{{parent}}</span> {{name}}</p>')
				}
			}).on('typeahead:selected', function(obj, datum) {
				$('#ajaxMenuLink').prop('action', datum['url']).submit();
			});

			
			// Voucher Related
			listener.simple_combo('alt v', function(e) {
				url = $('#NewVoucher').attr('href');
				if (url) window.location = url;
			});

			listener.simple_combo('alt s', function(e) {
				url = $('#NewSubVoucher').attr('href');
				if (url) window.location = url;
			});

			// Search Focus
			listener.simple_combo('ctrl space', function(e) {
				$('#ajaxMenu').focus();
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


	
	<!-- FilePond init script -->
    <script>
    	// Register plugins
	    FilePond.registerPlugin(
	        FilePondPluginFileValidateSize,
	        FilePondPluginImageExifOrientation,
	        FilePondPluginImageCrop,
	        FilePondPluginImageResize,
	        FilePondPluginImageTransform,
	        FilePondPluginFileValidateType
	    );

	    // Set default FilePond options
	    FilePond.setOptions({
	    	// maximum allowed file size
	        maxFileSize: '10MB',
	        maxTotalFileSize: '25MB',
	        // File Encode
	        allowFileEncode: true,
	        // crop the image to a 1:1 ratio
	        // upload to this server end point
	        server: '<?php echo base_url('utilities/upload') ?>'
	    });

	    const inputElements = document.querySelectorAll('input[name="file"]');
    	Array.from(inputElements).forEach(inputElement => {
      		//FilePond.create(inputElement);
      		FilePond.create(inputElement);
	    });

	    // Turn a file input into a file pond
	    
    </script>
    <script type="text/javascript">

    	jQuery(document).on('click', 'button#Delete', function(event) {

    		var id = $('input[name=id]').val();

    		Swal.fire({
		      title: "Are you sure?",
		      text: "You want to Delete this record...!",
		      icon: "warning",
		      showCancelButton: true,
		      confirmButtonColor: "#3085d6",
		      cancelButtonColor: "#d33",
		      confirmButtonText: "Yes, Delete it...!",
		      cancelButtonText: "Cancel",
		      buttonsStyling: true
		    }).then(function(result) {
		    	if (result.value) {
					$.ajax({
			            type: "post",
			            url: "<?php echo site_url($this->_clspath.$this->_class) ?>/delete/"+id,
			            success: function(response){
			            	var answer = JSON.parse(JSON.stringify(response));
			            	switch ( answer.status ) {
		                        case 'success' :
		                            Swal.fire({
							          title: 'Wow...!',
							          html:  ""+answer.msg+"",
							          icon: "success",
							        }).then(function() {
							    		location.reload();
							    	});
							    break;
		                        case 'warning' :
		                        	Swal.fire({
							          title: 'Ohhh...!',
							          html:  ""+answer.msg+"",
							          icon: "warning",
							        });	
		                        break;
		                        case 'error' :
		                        	Swal.fire({
							          title: 'Opps...!',
							          html:  ""+answer.msg+"",
							          icon: "error",
							        });	
		                        break;
		                    }	
		                },
			            error: function (result) {
			                Swal.fire({
					          title: "Somathing Wrong...!",
					          text: "Your Record is not Deleted, Try Again)",
					          icon: "error",
					        });
			            }
			        });
				} 
				else if (result.dismiss === Swal.DismissReason.cancel) {
			        Swal.fire({
			          title: "Cancelled",
			          text: "Your Records is safe :)",
			          icon: "error",
			        });
			    }
		    });

		    event.preventDefault();
			event.stopPropagation();
		});

    	$('.select2').select2();

    	$('.select2-selection__rendered').hover(function () {
		    $(this).removeAttr('title');
		});
		$(document).on('select2:open', () => {
	    	document.querySelector('.select2-search__field').focus();
	    });
    	
    </script>


</body>
</html>
