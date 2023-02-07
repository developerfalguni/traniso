<style>
a.list-group-item { padding: 5px; }
a.list-group-item.active { color: #ffffff !important; }
}
</style>

<div id="modal-import" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php 
			echo form_open($this->_clspath.'jobs/importJobs/'.$job_id['id'], 'id="VisualImport"');
			echo '<input type="hidden" name="manual" value="0" id="Manual" />';
		?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Import from VisualImpex / Manual Job</h3>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<h6>Select VisualImpex Export Job</h6>
					<input type="text" class="form-control form-control-sm" name="job_no" value="" id="ajaxVisualJob" />
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success pull-left" id="CreateManual">Create Manual</button>
				<button type="submit" class="btn btn-success">Import from Visual</button>
			</div>
			</form>
		</div>
	</div>
</div>

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
		<?php anchor($this->_clspath.'jobs/edit/0', '<i class="fa fa-plus"></i> Add', 'class="btn btn-xs btn-success"'); ?>
	</div>
	
	<!-- <div class="card-body"></div> -->

	<div class="row">
		<div class="col-lg-2 col-md-3 col-sm-4">
		<?php 
		if (isset($jobs) && isset($jobs['links'])) {
			if ($jobs['links']) {
				$jid = $jobs['id'];
				$url = $jobs['url'];
				
				echo '<div class="list-group">
					' . anchor($url."jobs/edit/$jid", $jobs['id2_format'], 'class="list-group-item ' . ($this->_class == "jobs" ?  ' active' : null) . '"');
					echo anchor($url."bl_master/edit/".$jid, 'BL Master', 'class="red list-group-item alignright' . ($this->_class == "bl_master" ?  ' active' : null) . '"') . 
					anchor($url."cargo_arrival/edit/".$jid, 'Cargo Carting', 'class="red list-group-item alignright' . ($this->_class == "cargo_arrival" ?  ' active' : null) . '"') . 
					anchor($url."stuffing/index/$jid", 'Stuffing Details', 'class="red list-group-item alignright' . ($this->_class == 'stuffing' ? ' active' : '') . '"') . 
					anchor($url."gatein/index/$jid", 'Gate In', 'class="red list-group-item alignright' . ($this->_class == 'gatein' ? ' active' : '') . '"') . 
					anchor($url."docin/index/$jid", 'Document Handover', 'class="red list-group-item alignright' . ($this->_class == 'docin' ? ' active' : '') . '"') . 
					anchor($url."onboard/index/$jid", 'Onboard Details', 'class="red list-group-item alignright' . ($this->_class == 'onboard' ? ' active' : '') . '"') . 
					anchor($url."attached_document/index/".$jid, 'Documents', 'class="red list-group-item alignright' . ($this->_class == "attached_document" && $child_job_id['id'] == 0 ?  ' active' : null) . '"') . 
					//anchor($url."photo/index/$jid", 'Photos', 'class="red list-group-item alignright' . ($this->_class == 'photo' ? ' active' : '') . '"') . 
					//anchor($url."drafts/index/".$jid, 'Drafts', 'class="red list-group-item alignright' . ($this->_class == "drafts" ?  ' active' : null) . '"') . 
					anchor($url."job_expense/index/".$jid, 'Job Expenses', 'class="red list-group-item alignright' . ($this->_class == "job_expense" ?  ' active' : null) . '"');

				foreach ($jobs['child_jobs'] as $cj) {
					echo anchor($url."/child_job/edit/$jid/".$cj['id'], (strlen($cj['vi_job_no']) == 0 ? 'Missing Job No' : $cj['vi_job_no']), 'class="orange list-group-item' . ($this->_class == "child_job" && $child_job_id['id'] == $cj['id'] ? ' active' : '') . '"');
					if ($child_job_id['id'] == $cj['id']) {
						//<li class="alignright' . ($this->_class == 'invoice' && $child_job_id['id'] == $cj['id'] ? ' active' : '') . '">' . anchor($url."/invoice/index/$jid/".$cj['id'], 'Invoices', 'class="red"') . '</li>
						
						echo anchor($url."/container/index/$jid/".$cj['id'], 'Containers', 'class="red list-group-item alignright' . ($this->_class == 'container' && $child_job_id['id'] == $cj['id'] ? ' active' : '') . '"') . 
						anchor($url."/attached_document/index/$jid/".$cj['id'], 'Documents', 'class="red list-group-item alignright' . ($this->_class == 'attached_document' && $child_job_id['id'] == $cj['id'] ? ' active' : '') . '"') . 
						anchor("/tracking/icegate_sb/index/".$cj['id'], "IceGate", 'class="red list-group-item alignright" target="_blank"');
					}
				}

				if ($jid > 0)
					echo '<a href="#modal-import" class="green list-group-item" data-toggle="modal"><i class="fa fa-plus"></i> Add Visual Job</a>';
				echo '
				</div>';
			}
		} 
		?>
		</div>

		<div class="col-lg-10 col-md-9 col-sm-8">
			<?php if (isset($jobs) && count($jobs) > 0) : ?>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Vessel - <?php echo $jobs['terminal_code'] ?></label><br />
						<h5><?php echo $jobs['vessel_name'] ?></h5>
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">ETA</label><br />
						<h5><?php echo $jobs['eta_date'] ?></h5>
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">ETD</label><br />
						<h5><?php echo $jobs['etd_date'] ?></h5>
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Gate Cutoff</label><br />
						<h5><?php echo $jobs['gate_cutoff_date'] ?></h5>
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Doc Cutoff</label><br />
						<h5><?php echo $jobs['doc_cutoff_date'] ?></h5>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Subtype</label><br />
						<?php 
						$job_sub_type = $this->kaabar->getField('jobs', $jobs['id'], 'id', 'sub_type');
						$label_class = $this->export->getLabelClass();
						$values = explode(',', $job_sub_type);
						foreach ($values as $v) {
							echo '<span class="nowrap label ' . $label_class[$v] . '">' . substr($v, 0, 1) . '</span> ';
						} ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label"><?php echo $jobs['stuffing_type'] ?> Stuffing</label><br />
						<h5><?php echo $jobs['stuffing_place'] ?></h5>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Cargo</label><br />
						<h5><?php echo $jobs['cargo_name'] ?></h5>
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Packaging</label><br />
						<h5><?php echo $jobs['unit_code'] ?></h5>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label"><?php echo $jobs['cargo_type'] ?></label><br />
						<h5><?php echo $jobs['containers'] ?></h5>
					</div>
				</div>
			</div>
			<hr />

		<?php 
			endif;

			$this->load->view($job_page);
		?>
		</div>
	</div>
</div>

<script id="AC_VisualJob" type="text/x-handlebars-template">
<li><a>{{Job_No}} <span class="tiny"><span class="orange">{{PARTY_NAME}}</span> {{Impx_PCode}}</span></a>
</script>

<script type="text/javascript">
$(document).ready(function() {
	$("#CreateManual").on('click', function() {
		$('#Manual').val(1);
		$("form#VisualImport").submit();
	});

	$("#ajaxVisualJob").autocomplete({
		appendTo: '#modal-import',
		source: '<?php echo site_url('/utilities/vi/ajaxExportJobs') ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#ajaxVisualJob").val( ui.item.Job_No);
			return false;
		},
		select: function(event, ui) {
			$("#ajaxVisualJob").val(ui.item.Job_No);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		var source   = $("#AC_VisualJob").html();
		var template = Handlebars.compile(source);
		var html     = template(item);
		return $(html).appendTo(ul);
	};
});
</script>