<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
			<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
			<?php echo anchor("#", 'Delete', 'class="btn btn-danger" id="DeleteUrl"') ?>
			</div>
		</div>
	</div>
</div>


<table class="table table-striped">
<thead>
<tr>
	<th width="24px">No</th>
	<th width="80px">Date</th>
	<th>Document</th>
	<th>Pages</th>
	<th>Remarks</th>
	<th width="60px">Received</th>
	<th width="60px">Attached</th>
	<th width="60px">Required</th>
	<th width="24px" class="aligncenter"><i class="icon-trash"></i></th>
</tr>
</thead>

<tbody>
<?php
	foreach($documents as $doc) {
		echo '<tr>
	<td>' . (strlen($doc['file']) == 0 ? 
		anchor($this->_clspath.$this->_class.'/attach/'.$doc['job_id'], $doc['sr_no']) :
		anchor($this->_clspath.$this->_class.'/edit/'.$doc['job_id'].'/'.$doc['id'].'/'.intval($doc['pages']), $doc['sr_no'])) . '</td>
	<td>' . $doc['date'] . '</td>
	<td>' . $doc['name'] . '</td>
	<td>' . $doc['pages'] . '</td>
	<td class="tiny">' . $doc['remarks'] . '</td>';
	if ($doc['received_date'] == '00-00-0000')
		echo '<td class="aligncenter"></td>';
	else 
		echo '<td class="aligncenter"><span class="label label-success">' . $doc['received_date'] . '</span></td>';

	echo '<td class="aligncenter">' . (strlen($doc['file']) > 0 ? '<i class="icon-paperclip"></i>' : '') . '</td>
	<td class="aligncenter"><span class="label ' . $label_class[$doc['is_compulsory']] . '">' . $doc['is_compulsory'] . '</span></td>
	<td><a href="javascript: deleteDocument(' . $doc['id'] . ')" class="btn btn-danger btn-xs"><i class="icon-trash"></i></a></td>
</tr>';
	}
?>
</tbody>
</table>

<script>
function deleteDocument(id) {
	$("a#DeleteUrl").attr("href", '<?php echo base_url($this->_clspath.$this->_class.'/delete/'.$job_id) ?>/'+id);
	$("#modal-delete").modal();
}

$(document).ready(function() {
	$('table.table-striped tbody tr td').bind("click", function() {
		if ($(this).children('a').size() == 1) {
			window.location = $(this).children('a').attr('href');
		}
		else if ($(this).children('a').size() == 0 &&
			$(this).parents('tr').children('td').children('a:first').size() == 1) {
			window.location = $(this).parents('tr').children('td').children('a:first').attr('href');
		}
	});
});
</script>