<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo isset($page_title) ? humanize($page_title) : (isset($page) ? humanize($page) : "Untitled") ?></title>
	<meta name="description" content="Welcome to KAABAR Shipping Solutions" />
	<meta name="author" content="Chetan Patel" />

	<!-- Styles -->
	<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" />
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	</style>

	<!-- Le fav and touch icons -->
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
</head>

<body>

<?php if (isset($page)) $this->load->view($page); ?>

</body>
</html>
