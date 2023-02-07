
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
echo form_open($this->_clspath.$this->_class.'/index/'.$voucher_id['id']);
echo form_hidden($voucher_id);
echo '<input type="hidden" name="attach" value="' . $md5 . '" />';
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
				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Company (FY)</label>
						<h5><?php echo $default_company['code'] . ' (' . str_replace('_', '-', $default_company['financial_year']) .  ')' ?></h5>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Voucher Type</label>
						<h5><?php echo $voucher['voucher_book_code'] ?></h5>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Voucher No</label>
						<h5><?php echo $voucher['id2'] ?></h5>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Amount</label>
						<h5><?php echo inr_format($voucher['amount']) ?></h5>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Debit</label>
						<h5><?php echo $voucher['debit_account'] ?></h5>
					</div>
				</div>
			</div>
		</fieldset>

		<div class="row">
			<div class="col-md-4">
				<div class="well" style="padding: 8px 0;">
					<ul class="nav nav-list">
						<li class="dropdown-header">Attached Documents</li>
						<?php foreach ($documents as $doc) {
							echo '<li' . ($doc['id'] == $id['id'] ? ' class="active"' : '') . '>' . anchor($this->_clspath.$this->_class.'/index/'.$doc['voucher_id'].'/'.$doc['id'], $doc['datetime']) . '</li>';
						} ?>
					</ul>
				</div>

				<table class="table table-condensed table-striped">
				<thead>
				<tr>
					<th>Pending Documents</th>
				</tr>
				</thead>

				<tbody>
				<?php
					foreach($pending as $pending_md5 => $doc) {
						echo '<tr>
					<td>' . anchor($this->_clspath.$this->_class.'/index/'.$voucher_id['id'].'/0/1/'.$pending_md5, $doc['name']) . '<br /><span class="tiny grayDark">Size: <span class="red">' . $doc['size'] . '</span>, Date: <span class="red">' . date('d-M-Y h:i A', $doc['date']) . '</span></span>' . '</td>
				</tr>';
					}
				?>
				</tbody>
				</table>
			</div>

			<div class="col-md-8" id="PreviewArea">
				<h3 id="Loading">Loading... <img src="/assets/css/images/loading.gif" id="Loading" /></h3>
				<?php if (isset($view)) { ?>

				<div class="card card-default">
					<div class="card-header">
						<span class="card--links"><?php echo ($id['id'] == 0 ? 
						'<button type="button" class="btn btn-success btn-xs" id="Update" onclick="javascript: submit();"><i class="icon-arrow-left"></i> Attach</button> ' : 
						'<button type="button" class="btn btn-danger btn-xs" onclick="javascript: detachDocument(' . $id['id'] . ')"><i class="icon-arrow-right"></i> Detach</button> '); ?></span>
						<h3 class="card-title"><?php echo 'Document <small>' . $view['name'] . '</small>' ?></h3>
					</div>
					
					<div class="card-body">
						
					<?php if (strtolower($view['type']) == 'pdf') {
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
				} ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</form>

<script>
function detachDocument(id) {
	$("a#DetachUrl").attr("href", '<?php echo base_url($this->_clspath.$this->_class.'/detach/'.$voucher_id['id']) ?>/'+id);
	$("#modal-detach").modal();
}
</script>