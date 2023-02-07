<?php if (! isset($view)) : ?>

<table class="table table-striped table-rowselect">
<thead>
<tr>
	<th>Pending Document</th>
	<th>Size</th>
	<th>Date</th>
</tr>
</thead>

<tbody>
<?php
	foreach($pending as $pending_md5 => $doc) {
		echo '<tr>
	<td>' . anchor($this->_clspath.$this->_class.'/index/'.$pending_md5, $doc['name']) . '</td>
	<td>' . $doc['size'] . '</td>
	<td>' . date('d-M-Y h:i A', $doc['date']) . '</td>
</tr>';
	}
?>
</tbody>
</table>

<?php else :

echo form_open($this->uri->uri_string(), 'id="MainForm"');
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
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Select Master Job</label>
						<input type="hidden" name="job_id" value="" id="JobID" />
						<input type="text" class="form-control form-control-sm" value="" id="JobNo" />
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<h6 class="data_heading">Select Visual Job</h6>
						<input type="hidden" name="child_job_id" value="" id="ChildJobID" />
						<input type="text" class="form-control form-control-sm" value="" id="ChildJobNo" />
					</div>
				</div>
			</div>
		</fieldset>
	
		<div class="row">
			<div class="col-md-4">
				<table class="table table-condensed table-striped">
				<thead>
					<th width="24px">Page</th>
					<th>Document Type</th>
					<th>Doc. No</th>
				</thead>

				<tbody>
				<tr id="templateRow" style="display: none">
					<td class="aligncenter big"></td>
					<td><select class="form-control form-control-sm DocumentType col-md-12"></select></td>
					<td><input type="text" class="form-control form-control-sm DocNo col-md-12" value="" /></td>
				</tr>
				</tbody>
				</table>
			</div>

			<div class="col-md-8">
				<h3 id="Loading">Loading... <img src="/assets/css/images/loading.gif" id="Loading" /></h3>

			<?php 
				if (strtolower($view['type']) == 'pdf')
					echo '<canvas id="the-canvas"></canvas>';
				else
					echo '<img src="' . $view['url'] . '" />';
			?>
			</div>
		</div>
	</div>
</div>

</form>

<script>
function loadPages(pages) {
	for(i = 0; i < pages; i++) {
		$("#templateRow")
			.clone()
			.removeAttr("id")
			.removeAttr("style")
			.find('td:eq(0)')
				.text(i+1)
				.end()
			.find('select')
				.attr('name', 'document_id['+(i+1)+']')
				<?php if (strtolower($view['type']) == 'pdf') echo ".attr('onfocus', 'javascript: getPage('+(i+1)+')')"; ?>
				.end()
			.find('input')
				.attr('name', 'doc_no['+(i+1)+']')
				.end()
			.appendTo($("#templateRow").parent());
	}

	$(".DocNo").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxDocNos') ?>/0',
		minLength: 0
	});
}

<?php if (strtolower($view['type']) == 'pdf') : ?>
'use strict';
PDFJS.workerSrc = '<?php echo base_url('assets/pdfjs/build/pdf.worker.js') ?>';

PDFJS.getDocument('<?php echo str_replace("'", "\'", $view['url']) ?>').then(function(pdf) {
	loadPages(pdf.numPages);

	pdf.getPage(<?php echo $page_no ?>).then(function(page) {
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
	PDFJS.getDocument('<?php echo str_replace("'", "\'", $view['url']) ?>').then(function(pdf) {
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
<?php else: ?>
	loadPages(1);
<?php endif; ?>

$(document).ready(function() {
	$("#JobNo").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJobs') ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#JobNo").val(ui.item.id2_format + ' - ' + ui.item.party_name);
			return false;
		},
		select: function(event, ui) {
			$("#JobNo").val(ui.item.id2_format + ' - ' + ui.item.party_name);
			$("#JobID").val(ui.item.id);
			$("#ChildJobID").val(0);
			$("#ChildJobNo").val('');
			$("#ChildJobNo").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxChildJobs') ?>/" + ui.item.id);
			$(".DocNo").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxDocNos') ?>/" + ui.item.id);
			$.get('<?php echo base_url($this->_clspath.$this->_class."/getJobDocuments") ?>/'+ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#JobID").val(0);
				$("#JobNo").val('');
				$("#ChildJobID").val(0);
				$("#ChildJobNo").val('');
				$("#ChildJobNo").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxChildJobs/0') ?>");
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny">' + item.id2_format + ' <span class="orange">' + item.party_name + '</span> ' + item.invoice_no + ' <span class="blue">' + item.container_no + '</span></span></a>')
			.appendTo(ul);
	};

	$("#ChildJobNo").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxChildJobs') ?>/0',
		minLength: 0,
		focus: function(event, ui) {
			$("#ChildJobNo").val(ui.item.vi_job_no);
			return false;
		},
		select: function(event, ui) {
			$("#ChildJobID").val(ui.item.id);
			$("#ChildJobNo").val(ui.item.vi_job_no);
			$.get('<?php echo base_url($this->_clspath.$this->_class."/getJobDocuments") ?>/' + $("#JobID").val() + '/' + ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#ChildJobID").val(0);
				$("#ChildJobNo").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.vi_job_no + ' <span class="orange tiny">' + item.sb_no + ' / ' + item.sb_date + '</span></a>')
			.appendTo(ul);
	};
});
</script>

<?php endif; ?>