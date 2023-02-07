<?php 
echo form_open($this->_clspath.$this->_class.'/attach/'.$job_id['id'], 'class="form-horizontal"');
echo form_hidden($job_id);
echo '<input type="hidden" name="attach" value="' . $md5 . '" />';
?>

<div class="card-body">

<div class="row">
	<div class="col-md-4">
		<?php echo start_panel('Attach Documents', '', 'p-0'); ?>
			<table class="table">
				<thead>
					<tr>
						<th width="100px" class="p-030">Date</th>
						<th class="p-030">Document</th>
						<th class="p-030">Remarks</th>
						<th width="64px" class="p-030">Pages</th>
						<th width="24px" class="p-030"><i class="fa fa-trash-alt"></i></th>
					</tr>
				</thead>
				<tbody>
					<tr id="1" class="d-none">
						<td><input type="text" class="form-control form-control-sm Text DateTime" name="date[]" value="" size="10" /></td>
						<td><input type="hidden" name="document_id[]" value="" />
							<input type="hidden" name="document_type_id[]" value="" />
							<input type="text" class="form-control form-control-sm" value="" /></td>
						<td><input type="text" class="form-control form-control-sm" name="remarks[]" value="" /></td>	
						<td><input type="text" class="form-control form-control-sm" name="pages[]" value="" /></td>
						<td><span id="1"><a href="#" class="btn btn-danger btn-sm"><i class="icon-minus"></i></a></span></td>
					</tr>
					<tr id="Blank">
						<td class="p-030"><input type="text" class="form-control form-control-sm Text DateTime" name="blank_date" value="<?php echo date('d-m-Y') ?>" size="10" /></td>
						<td class="p-030"><input type="hidden" name="blank_did" value="" id="DocumentID" />
							<input type="hidden" name="blank_dtid" value="" id="DocumentTypeID" />
							<input type="text" class="form-control form-control-sm" value="" id="DocumentName" /></td>
						<td class="p-030"><input type="text" class="form-control form-control-sm" name="blank_remarks" value="" id="Remarks" /></td>
						<td class="p-030"><input type="text" class="form-control form-control-sm" name="blank_pages" value="" /></td>
						<td class="p-030"><a href="javascript:make_copy(1)" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></a></td>
					</tr>
				</tbody>
			</table>

		<?php echo end_sl_panel(); ?>

		<div class="card-footer pl-2">
			<button type="submit" class="btn btn-primary btn-sm" id="Add">Add</button>&nbsp;
			<button type="button" class="btn btn-success btn-sm" id="Update" onclick="javascript: submit();"><i class="icon-arrow-up"></i> Attach</button></td>
		</div>
		<?php echo end_panel(); ?>

		<table class="table table-striped">
		<thead>
		<tr>
			<th>Pending Documents</th>
		</tr>
		</thead>

		<tbody id="DivFrame">
		<?php
			foreach($pending as $pending_md5 => $doc) {
				echo '<tr>
			<td>' . anchor($this->_clspath.$this->_class.'/attach/'.$job_id['id'].'/'.$pending_md5, $doc['name']) . '<br /><span class="tiny grayDark">Size: <span class="red">' . $doc['size'] . '</span>, Date: <span class="red">' . date('d-M-Y h:i A', $doc['date']) . '</span></span>' . '</td>
		</tr>';
			}
		?>
		</tbody>
		</table>
	</div>

	<div class="col-md-8" id="PreviewArea">
		<h3 id="Loading">Loading... <img src="/assets/vendors/css/images/loading.gif" id="Loading" /></h3>
		<?php 
		if (isset($view)) {
			echo start_panel('Document <small>' . $view['name'] . '</small>', '', 'nopadding', '<div class="buttons" id="PageLinks"></div>');

			if (strtolower($view['type']) == 'pdf') {
				echo "
				<canvas id=\"the-canvas\">
					<script>
					'use strict';
					PDFJS.workerSrc = '" . base_url('assets/vendors/pdfjs/build/pdf.worker.js') . "';
					PDFJS.getDocument('" . str_replace("'", "\'", $view['url']) . "').then(function(pdf) {
						
						var pages = pdf.numPages;
						var i;
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
							$('#Loading').addClass('d-none');
						});
					});

					function getPage(page_no) {
						$('.page_btns').removeClass('btn-primary').addClass('btn-info');
						$('#page_btn_'+page_no).removeClass('btn-info').addClass('btn-primary');
						PDFJS.getDocument('" . str_replace("'", "\'", $view['url']) . "').then(function(pdf) {
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
					</script>
				</canvas>";
			}
			else if (in_array($view['type'], array('jpeg', 'jpg', 'png', 'bmp', 'gif')))
				echo '<img src="' . $view['url'] . '" />';
			else if (in_array($view['type'], array('prn', 'txt')))
				echo '<pre>' . $view['contents'] . '</pre>';
			else
				echo $view['contents'];
		}

	echo end_panel(); 
?>
	</div>
</div>
</form>

<script>
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

<?php if (isset($view)) : ?>
	$("#DocumentName").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxDocuments/'.$job_id['id']) ?>",
		minLength: 0,
		open: function(event, ui) {
            $(this).autocomplete('widget').css({
                "width": 400
            });
        },
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
			.append('<a><span class="' + (item.is_compulsory == 'Yes' ? 'red' : 'orange') + '">' + item.name + '</span> <span class="tiny">' + item.remarks + '</span></a>')
			.appendTo(ul);
	};
<?php endif; ?>
});
</script>