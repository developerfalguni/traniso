<?php 
echo form_open($this->_clspath.$this->_class.'/attach/'.$job_id['id'], 'class="form-horizontal" id="MainForm"');
echo form_hidden($job_id);
echo '<input type="hidden" name="attach" value="' . $md5 . '" />';
?>

<div class="row">
	<div class="col-md-4">
		<?php echo start_panel('Attach Documents', '', 'nopadding'); ?>
		<table class="table table-condensed table-striped DataEntry">
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
		<tr class="TemplateRow">
			<td><input type="text" class="form-control form-control-sm Text DateTime Unchanged Validate" name="date[]" value="" /></td>
			<td><input type="hidden" class="form-control form-control-sm DocumentID" name="document_id[]" value="" />
				<input type="hidden" class="form-control form-control-sm DocumentTypeID Validate" name="document_type_id[]" value="" />
				<input type="text" class="form-control form-control-sm DocumentName Validate Focus" value="" /></td>
			<td><input type="text" class="form-control form-control-sm" name="remarks[]" value="" /></td>	
			<td><input type="text" class="form-control form-control-sm" name="pages[]" value="" /></td>
			<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
		</tr>
		</tbody>
		</table>

		<div class="form-actions">
			<button type="button" class="btn btn-success" id="Update"><i class="icon-arrow-up"></i> Attach</button></td>
		</div>
		<?php echo end_panel(); ?>

		<table class="table table-condensed table-striped">
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
		<h3 id="Loading">Loading... <img src="/assets/css/images/loading.gif" id="Loading" /></h3>
		<?php 
		if (isset($view)) {
			echo start_panel('Document <small>' . $view['name'] . '</small>', '', 'nopadding', '<div class="buttons" id="PageLinks"></div>');

			if (strtolower($view['type']) == 'pdf') {
				echo "
			<canvas id=\"the-canvas\">
<script>
'use strict';
PDFJS.workerSrc = '" . base_url('assets/pdfjs/build/pdf.worker.js') . "';
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
		$('#Loading').addClass('hide');
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
			else {
				echo '<img src="' . $view['url'] . '" />';
			}
		}

	echo end_panel(); 
?>
	</div>
</div>
</form>

<script>
$(document).ready(function() {
<?php if (isset($view)) : ?>
	$('.DataEntry').on('keydown.autocomplete', '.DocumentName', function(event, items) {
		var id      = $(this).parent('td').parent('tr').find('.DocumentID');
		var typeid  = $(this).parent('td').parent('tr').find('.DocumentTypeID');
		var remarks = $(this).parent('td').parent('tr').find('.Remarks');
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxDocuments/'.$job_id['id']) ?>",
			minLength: 0,
			open: function(event, ui) {
	            $(this).autocomplete('widget').css({
	                "width": 400
	            });
	        },
			focus: function(event, ui) {
				$(this).val(ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(typeid).val(ui.item.document_type_id);
				$(this).val(ui.item.name);
				$(remarks).val(ui.item.remarks);
				return false;
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><span class="' + (item.is_compulsory == 'Yes' ? 'red' : 'orange') + '">' + item.name + '</span> <span class="tiny">' + item.remarks + '</span></a>')
				.appendTo(ul);
		};
	});
<?php endif; ?>
});
</script>