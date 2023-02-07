
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

<div class="row">
	<div class="col-md-4">
		<?php echo start_panel('Attach Documents', '', 'nopadding', '<div class="buttons">' . 
			anchor('master/party/edit/'.$party_id['party_id'], 'Party', 'class="btn btn-sm btn-info"') .
		'</div>'); ?>
		<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<th width="100px">Date</th>
			<th>Document Type</th>
			<th width="24px" class="aligncenter"><i class="icon-trash"></i></th>
		</tr>
		</thead>

		<tbody>
		<?php foreach ($documents as $doc) {
			echo '<tr>
			<td>' . $doc['date'] . '</td>
			<td>' . anchor($this->_clspath.$this->_class.'/index/'.$doc['party_id'].'/'.$doc['id'].'/'.(intval($doc['pages']) == 0 ? 1 : intval($doc['pages'])), $doc['name']) . '</td>
			<td><a href="javascript: deleteDocument(' . $doc['id'] . ')" class="btn btn-danger btn-sm"><i class="icon-trash"></i></a></td>
		</tr>';
		} ?>
		</tbody>
		</table>
		<?php echo end_panel(); ?>
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

<script>
function deleteDocument(id) {
	$("a#DeleteUrl").attr("href", '<?php echo base_url($this->_clspath.$this->_class.'/detach/'.$party_id['party_id']) ?>/'+id);
	$("#modal-delete").modal();
}
</script>