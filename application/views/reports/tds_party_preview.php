<html>
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	</style>
</head>

<body>

<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title . ' ' . $page_desc ?></h4>

<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Party</th>
	<th>Address</th>
	<th>PAN No</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
foreach ($rows as $r) {
	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="nowrap">' . $r['party_name'] . '</td>
	<td>' . $r['address'] . '</td>
	<td>' . $r['pan_no'] . '</td>
</tr>
';
}
?>
</tfoot>
</tbody>
</table>

</body>
</html>