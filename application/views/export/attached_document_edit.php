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

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools">
  			<ol class="breadcrumb float-sm-right m-0">
      			<li class="breadcrumb-item"><a href="#"><?php echo anchor('main','Dashboard') ?></a></li>
      			<li class="breadcrumb-item"><?php echo humanize(clean($this->_clspath)) ?></li>
      			<li class="breadcrumb-item active mr-1"><?php echo humanize($this->_class) ?> edit</li>
    		</ol>
		</div>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Date</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo ($document['date'] != '00-00-0000' ? $document['date'] : '') ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Doc No</label>
						<input type="text" class="form-control form-control-sm" name="doc_no" value="<?php echo $document['doc_no'] ?>" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Pages</label>
						<input type="text" class="form-control form-control-sm" name="pages" value="<?php echo $document['pages'] ?>" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Compulsory</label>
						<h5><?php echo $document['is_compulsory'] ?></h5>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">File Pinned</label>
						<h5><?php echo $document['file'] ?></h5>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label">Remarks</label>
						<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $document['remarks'] ?>" />
					</div>
				</div>
			</div>
		</fieldset>

	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-md-6">
			<?php 
			if (Auth::isAdmin()) echo '<button type="button" class="btn btn-success" id="Update" onclick="javascript: submit();">Update</button>&nbsp;';
				echo anchor($document['url'], '<i class="icon-download"></i> Download', 'class="btn btn-primary"');
			 ?>
			</div>
			<div class="col-md-6 alignright">
			<a href="javascript: detachDocument(<?php echo $id['id'] ?>)" class="btn btn-danger"><i class="icon-cancel"></i> Detach</a>
			</div>
		</div>
	</div>
</div>

<fieldset>
	<h3 id="Loading">Loading... <img src="/assets/css/images/loading.gif" id="Loading" /></h3>
	<div id="PageLinks"></div>

	<?php 
	if (strtolower(substr($document['file'], -3)) == 'pdf') {
		echo "
			<canvas id=\"the-canvas\" />
<script>
var PreviewAreaWidth = $('#PreviewArea').width();

'use strict';
PDFJS.workerSrc = '" . base_url('assets/pdfjs/build/pdf.worker.js') . "';
PDFJS.getDocument('" . $document['url'] . "').then(function(pdf) {
	
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
	PDFJS.getDocument('" . $document['url'] . "').then(function(pdf) {
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
		else {
			echo '<img src="' . $document['url'] . '" />';
		}
?>
</fieldset>

</form>

<script>
function detachDocument(id) {
	$("a#DetachUrl").attr("href", '<?php echo base_url($this->_clspath.$this->_class.'/detach/'.$job_id['id']) ?>/'+id);
	$("#modal-detach").modal();
}
</script>