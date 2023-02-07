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

<?php 
echo form_open($this->uri->uri_string(), 'id="MainForm"'); 
echo form_hidden($job_id);
?>

<div class="card card-default">
	<div class="card-header">		
		<h3 class="card-title">Documents</h3>
	</div>
	
	<table class="table table-striped DataEntry">
		<thead>
		<tr>
			<th width="24px">No</th>
			<th width="160px">Date</th>
			<th>Document</th>
			<th>Doc No</th>
			<th width="80px">Pages</th>
			<th>Remarks</th>
			<th width="150px">Received</th>
			<th width="60px">Attached</th>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll" checkbox-class="SelectCheckbox"><i class="icon-checkbox-checked"></i></a></th>
			<th width="24px" class="aligncenter"><i class="icon-trash"></i></th>
		</tr>
		</thead>

		<tbody>
			<?php
				foreach($documents as $doc) {
					echo '<tr>
				<td>' . (strlen($doc['file']) == 0 ? 
					anchor($this->_clspath.'/attach_document/index', '0') :
					anchor($this->_clspath.$this->_class.'/edit/'.$doc['job_id'].'/'.$doc['child_job_id'].'/'.$doc['id'], $doc['sr_no'])) . '</td>
				<td>' . $doc['date'] . '</td>
				<td>' . $doc['name'] . '</td>
				<td>' . $doc['doc_no'] . '</td>
				<td>' . $doc['pages'] . '</td>
				<td class="tiny">' . $doc['remarks'] . '</td>';
				if ($doc['received_date'] == '00-00-0000')
					echo '<td class="aligncenter"></td>';
				else 
					echo '<td class="aligncenter"><span class="label label-success">' . $doc['received_date'] . '</span></td>';
				echo '<td class="aligncenter">' . (strlen($doc['file']) > 0 ? '<i class="icon-paperclip"></i>' : '') . '</td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'is_compulsory[]', 'value' => $doc['document_type_id'], 'checked' => ($doc['is_compulsory'] == 'Yes' ? true : false), 'class' => 'SelectCheckbox')) . '</td>
				<td><a href="javascript: deleteDocument(' . $doc['id'] . ')" class="btn btn-danger btn-sm"><i class="icon-trash"></i></a></td>
			</tr>';
				}
			?>

			<tr class="TemplateRow">
				<td></td>
				<td><div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate Unchanged" name="new_date[]" value="<?php echo date('d-m-Y') ?>" />
					</div>
				</td>
				<td><input type="hidden" class="form-control form-control-sm DocumentTypeID" name="new_document_type_id[]" value="" />
					<input type="text" class="form-control form-control-sm Document Validate Focus" name="new_document_name[]" value="" /></td>
				<!-- <td><input type="text" class="form-control form-control-sm" name="new_doc_no[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm" name="new_pages[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm" name="new_remarks[]" value="" /></td>
				<td><div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate" name="new_received_date[]" value="" />
					</div>
				</td> -->
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
			</tr>
		</tbody>
	</table>	

	<div class="card-footer">
		<button type="button" class="btn btn-success UpdateButton" id="Update">Update</button>
	</div>
</div>

</form>

<script>
function deleteDocument(id) {
	$("a#DeleteUrl").attr("href", '<?php echo base_url($this->_clspath.$this->_class.'/delete/'.$job_id['job_id']) ?>/'+id);
	$("#modal-delete").modal();
}

$(document).ready(function() {
	$(".DataEntry").on('keydown.autocomplete', '.Document', function(event, items) {
		var id = $(this).parent('td').parent('tr').find(".DocumentTypeID");
				
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxDocs') ?>",
			minLength: 2,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				return false;
			},
			response : function(event, ui) {
				if (ui.content.length === 0) {
					$(id).val(0);
					$(this).val('');					
				}
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><span class="blueDark">' + item.name + ' </span></a>')
				.appendTo(ul);
		};
	});

});
</script>