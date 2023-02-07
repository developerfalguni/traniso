<?php 



if (! isset($view)) : ?>

<table class="table table-condensed table-striped">
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
			<label class="control-label">Select Job</label>
				<input type="hidden" name="job_id" value="" id="JobID" />
				<input type="text" class="form-control form-control-sm" value="" id="JobNo" />

			<label class="control-label">Select Party</label>
				<input type="hidden" name="party_id" value="" id="PartyID" />
				<input type="text" class="form-control form-control-sm" value="" id="PartyName" />

			<label class="control-label">Select Staff</label>
				<input type="hidden" name="staff_id" value="" id="StaffID" />
				<input type="text" class="form-control form-control-sm" value="" id="StaffName" />	
		</fieldset>
		<br />

		<div class="row">
			<div class="col-md-4">
				<table class="table table-condensed table-striped">
				<thead>
					<th width="24px">Page</th>
					<th>Document Type</th>
				</thead>

				<tbody>
				<tr id="templateRow" style="display: none">
					<td class="aligncenter big"></td>
					<td><select class="form-control form-control-sm DocumentType col-md-12"></select></td>
				</tr>
				</tbody>
				</table>
			</div>

			<div class="col-md-8">
				<h3 id="Loading">Loading... <img src="/assets/css/images/loading.gif" id="Loading" /></h3>

		<?php 
			if (strtolower($view['type']) == 'pdf')
				echo '<canvas id="the-canvas"></canvas>';
			else if (in_array($view['type'], array('jpeg', 'jpg', 'png', 'bmp', 'gif')))
				echo '<img src="' . $view['url'] . '" />';
			else if (in_array($view['type'], array('prn', 'txt')))
				echo '<pre>' . $view['contents'] . '</pre>';
			else
				echo $view['contents'];
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
			.appendTo($("#templateRow").parent());
	}
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
		minLength: 3,
		focus: function(event, ui) {
			$("#JobNo").val(ui.item.party_name);
			return false;
		},
		select: function(event, ui) {
			$("#JobNo").val(ui.item.party_name);
			$("#JobID").val(ui.item.id);
			$("#PartyID").val(0);
			$("#PartyName").val('');
			$("#StaffID").val(0);
			$("#StaffName").val('');
			$.get('<?php echo base_url($this->_clspath.$this->_class."/getJobDocuments") ?>/'+ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#JobID").val(0);
				$("#JobNo").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.party_name + ' - <span class="blue">' + item.packages + ' / ' + item.net_weight + '</span><br /><span class="tiny"><span class="orange">' + item.bl_no + '</span> ' + item.bl_date + ' / <span class="orange">' + item.be_no + '</span> ' + item.be_date + '</span></a>')
			.appendTo(ul);
	};

	$("#PartyName").autocomplete({
		source: '<?php echo site_url('/master/party/ajax') ?>',
		minLength: 3,
		focus: function(event, ui) {
			$("#PartyName").val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$("#PartyName").val(ui.item.name);
			$("#PartyID").val(ui.item.id);
			$("#JobID").val(0);
			$("#JobNo").val('');
			$("#StaffID").val(0);
			$("#StaffName").val('');
			$.get('<?php echo base_url($this->_clspath.$this->_class."/getPartyDocuments") ?>/'+ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#PartyID").val(0);
				$("#PartyName").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + '</a>')
			.appendTo(ul);
	};


	$("#StaffName").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxStaff') ?>',
		minLength: 3,
		focus: function(event, ui) {
			$("#StaffName").val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$("#StaffName").val(ui.item.name);
			$("#StaffID").val(ui.item.id);
			$("#JobID").val(0);
			$("#JobNo").val('');
			$("#PartyID").val(0);
			$("#PartyName").val('');
			$.get('<?php echo base_url($this->_clspath.$this->_class."/getStaffDocuments") ?>/'+ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#StaffID").val(0);
				$("#StaffName").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="orange">' + item.category + '</span> ' + item.name + '</a>')
			.appendTo(ul);
	};
});
</script>


<?php endif; ?>