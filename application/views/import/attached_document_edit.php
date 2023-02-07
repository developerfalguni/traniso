<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/email/'.$job_id['id'] . '/' . $id['id'], 'id="EmailForm"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Email Document</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="control-label">To</label>
						<input type="text" class="form-control form-control-sm" name="to" value="<?php echo isset($to_email) ? $to_email : '' ?>" id="ajaxEmail" />
					</div>

					<div class="form-group">
						<label class="control-label">CC</label>
						<input type="text" class="form-control form-control-sm" name="cc" value="" />
					</div>

					<div class="form-group">
						<label class="control-label">BCC</label>
						<input type="text" class="form-control form-control-sm" name="bcc" value="<?php echo Settings::get('smtp_user') ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Subject</label>
						<input type="text" class="form-control form-control-sm" name="subject" value="Ledger Account Statement" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm" name="message" rows="5"><?php echo "Dear Sir / Ma'am,\n\nKindly find the Document in attachment."; ?></textarea>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success"><i class="icon-envelope-o"></i> Send</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div id="modal-detach" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DETACH...?</p></div>
			<div class="modal-footer">
				<?php echo anchor("#", 'Detach', 'class="btn btn-danger" id="DetachUrl"') ?>
			</div>
		</div>
	</div>
</div>

<?php 
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($job_id);
echo form_hidden($id);
?>

<div class="row">
	<div class="col-md-4">
	<?php echo start_panel('Attached Documents', '', 'nopadding'); ?>
		<div class="well" style="padding: 0;">
	  		<ul class="nav nav-list">
				<?php foreach ($documents as $doc) {
					$pages = explode(',', $doc['pages']);
					echo '<li' . ($doc['id'] == $id['id'] ? ' class="active"' : '') . '>' . anchor($this->_clspath.$this->_class.'/edit/'.$job_id['id'].'/'.$doc['id'].'/'.$pages[0], $doc['name'] . ' <span class="tiny orange">' . $doc['pages'] . '</span>' . ' (<span class="tiny">' . $doc['remarks'] . '</span>)') . '</li>';
				} ?>
			</ul>
		</div>

		<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<th width="100px">Date</th>
			<th>Document</th>
			<th>Remarks</th>
			<th width="64px">Pages</th>
			<th width="24px"></th>
		</tr>
		</thead>

		<tbody>
		<tr id="1" class="hide">
			<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_date[]" value="" /></div></td>
			<td><input type="hidden" name="new_did[]" value="" />
				<input type="hidden" name="new_dtid[]" value="" />
				<input type="text" class="form-control form-control-sm" value="" /></td>
			<td><input type="text" class="form-control form-control-sm" name="new_remarks[]" value="" /></td>	
			<td><input type="text" class="form-control form-control-sm" name="new_pages[]" value="" /></td>
			<td><span id="1"><a href="#" class="btn btn-danger btn-sm"><i class="icon-minus"></i></a></span></td>
		</tr>

		<tr id="Blank">
			<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="blank_date" value="" /></div></td>
			<td><input type="hidden" name="blank_did" value="" id="DocumentID" />
				<input type="hidden" name="blank_dtid" value="" id="DocumentTypeID" />
				<input type="text" class="form-control form-control-sm" value="" id="DocumentName" /></td>
				<td><input type="text" class="form-control form-control-sm" name="blank_remarks" value="" id="Remarks" /></td>
			<td><input type="text" class="form-control form-control-sm" name="blank_pages" value="" /></td>
			<td><a href="javascript:make_copy(1)" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a></td>
		</tr>
		</tbody>
		</table>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary" id="Add">Add</button>&nbsp;
			<button type="button" class="btn btn-success" id="Update"><i class="icon-arrow-up"></i> Attach</button></td>
		</div>
	<?php echo end_panel(); ?>
	</div>

	<div class="col-md-8" id="PreviewArea">
	<?php echo start_panel($view['name'], '', 'nopadding', '<div class="buttons" id="PageLinks"></div>'); ?>
		<fieldset>
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Date</label>
						<div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo $view['date'] ?>" size="10" />
					</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Pages</label>
						<input type="text" class="form-control form-control-sm" name="pages" value="<?php echo $view['pages'] ?>" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Compulsory</label><br />
						<h5><?php echo $view['is_compulsory'] ?></h5>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">File Pinned</label><br />
						<h5><?php echo $view['file'] ?></h5>
					</div>
				</div>
			</div>

			<div class="row">
				<?php if (Auth::isAdmin()) : ?>
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Visible Only To</label>
						<?php echo form_dropdown('visible_user_ids[]', $this->messages->getUsers(), explode(',', $view['visible_user_ids']), 'class="SelectizeKaabar" multiple data-placeholder="Select User(s)..."') ?>
					</div>
				</div>

				<div class="col-md-6">
			<?php else : ?>
				<div class="col-md-12">
			<?php endif; ?>
					<div class="form-group">
						<label class="control-label">Remarks</label>
						<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $view['remarks'] ?>" />
					</div>
				</div>
			</div>
		</fieldset>

		<div class="form-actions">
			<?php if (Auth::isAdmin()) echo '<button type="button" class="btn btn-success" id="Update">Update</button>'; ?>
			<a href="javascript: detachDocument(<?php echo $id['id'] ?>)" class="btn btn-danger"><i class="icon-arrow-down"></i> Detach</a>
			<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i> Email</a>
		</div>

		<fieldset>
			<h3 id="Loading">Loading... <img src="/assets/css/images/loading.gif" id="Loading" /></h3>
			<div id="PageLinks"></div>

			<?php 
				if (strtolower(substr($view['file'], -3)) == 'pdf') {
					echo "
				<canvas id=\"the-canvas\" />
<script>
var PreviewAreaWidth = $('#PreviewArea').width();

'use strict';
PDFJS.workerSrc = '" . base_url('assets/pdfjs/build/pdf.worker.js') . "';
PDFJS.getDocument('" . $view['url'] . "').then(function(pdf) {
	
	var pages = pdf.numPages;
	for(i = 1; i <= pages; i++) {
		$('#PageLinks').append('<a href=\"javascript: getPage(' + i + ')\" class=\"btn btn-sm btn-info page_btns\" id=\"page_btn_'+ i +'\">' + i + '</a>&nbsp;');
	}

	pdf.getPage(" . $page_no . ").then(function(page) {
		var scale = 1.5;
		var viewport = page.getViewport(scale);
		var canvas = document.getElementById('the-canvas');
		var context = canvas.getContext('2d');
		canvas.height = viewport.height;
		canvas.width = viewport.width;
		var renderContext = {
			canvasContext: context,
			viewport: viewport
		};
		page.render(renderContext);
		$('#Loading').addClass('hide');
	});
});

function getPage(page_no) {
	$('.page_btns').removeClass('btn-primary').addClass('btn-info');
	$('#page_btn_'+page_no).removeClass('btn-info').addClass('btn-primary');
	PDFJS.getDocument('" . $view['url'] . "').then(function(pdf) {
		pdf.getPage(page_no).then(function(page) {
			var scale = 1.5;
			var viewport = page.getViewport(scale);
			var canvas = document.getElementById('the-canvas');
			var context = canvas.getContext('2d');
			canvas.height = viewport.height;
			canvas.width = viewport.width;
			var renderContext = {
				canvasContext: context,
				viewport: viewport
			};
			page.render(renderContext);
		});
	});
}
</script>";
				}
				else if (in_array($view['type'], array('jpeg', 'jpg', 'png', 'bmp', 'gif')))
					echo '<img src="' . $view['url'] . '" />';
				else if (in_array($view['type'], array('prn', 'txt'))) {
					echo '<pre>' . $view['contents'] . '</pre>';
					echo '<script>$("#Loading").addClass("hide");</script>';
				}
				else
					echo $view['contents'];
		
		echo '</fieldset>';
	echo end_panel(); 
?>
	</div>
</div>
</form>

<script>
function detachDocument(id) {
	$("a#DetachUrl").attr("href", '<?php echo base_url($this->_clspath.$this->_class.'/detach/'.$job_id['id']) ?>/'+id);
	$("#modal-detach").modal();
}

function make_copy(id) {
	var v0 = $("tr#Blank input:eq(0)").val();
	var v1 = $("tr#Blank input:eq(1)").val();
	var v2 = $("tr#Blank input:eq(2)").val();
	var v3 = $("tr#Blank input:eq(3)").val();
	var v4 = $("tr#Blank input:eq(4)").val();
	var v5 = $("tr#Blank input:eq(5)").val();

	if (!v0 || (!v1 || !v2) || !v5) return;
	
	if (id > 1) {
		$("tr#1").clone().insertBefore("tr#Blank").attr("id", id);
	}

	$("tr#Blank td a").attr("href", "javascript:make_copy("+(id+1)+")");
	$("#Add").unbind('click');
	$("#Add").on("click", function() {
		make_copy(id+1);
		return false;
	});

	$("tr#"+id+" input:eq(0)").val(v0);
	$("tr#"+id+" input:eq(1)").val(v1);
	$("tr#"+id+" input:eq(2)").val(v2);
	$("tr#"+id+" input:eq(3)").val(v3);
	$("tr#"+id+" input:eq(4)").val(v4);
	$("tr#"+id+" input:eq(5)").val(v5);
	
	$("tr#"+id+" td a").attr("href", "javascript:remove_copy("+id+")");
	$("tr#"+id).removeClass("hide");

	$("tr#Blank input:eq(1)").val('');
	$("tr#Blank input:eq(2)").val('');
	$("tr#Blank input:eq(3)").val('');
	$("tr#Blank input:eq(4)").val('');
	$("tr#Blank input:eq(5)").val('');
	$("tr#Blank input:eq(3)").focus();
}

function remove_copy(id) {
	if (id == 1) {
		$("tr#1 input").each(function(index) {
			$(this).val("");
		});
		$("tr#1").addClass("hide");
	}
	else {
		$("tr#"+id).remove();
	}
}

$(document).ready(function() {
	$("#Add").on("click", function() {
		make_copy(1);
		return false;
	});

	$("#DocumentName").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxDocuments/'.$job_id['id']) ?>",
		minLength: 0,
		focus: function(event, ui) {
			$("#DocumentName").val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$("#DocumentID").val(ui.item.id);
			$("#DocumentTypeID").val(ui.item.document_type_id);
			$("#DocumentName").val(ui.item.name);
			$("#Remarks").val(ui.item.remarks);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="' + (item.is_compulsory == 'Yes' ? 'red' : 'orange') + '">' + item.name + '</span> <span class="tiny">' + item.remarks + '</span>' + '</a>')
			.appendTo(ul);
	};

	$("#ajaxEmail").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxEmail/'.$job_id['id']) ?>",
		minLength: 0,
		focus: function(event, ui) {
			$("#ajaxEmail").val(ui.item.email);
			return false;
		},
		select: function(event, ui) {
			$("#ajaxEmail").val(ui.item.email);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + ' <span class="tiny orange">' + item.email + '</span></a>')
			.appendTo(ul);
	};
});
</script>