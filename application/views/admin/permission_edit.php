<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select User : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('userList', getSelectOptions('users', 'id', 'username') , $user_id, 'class="form-control mb-0" id="userSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-12">
				<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Search Filter</label>
							<div class="input-filter-container">
								<input type="search" id="input-filter" class="form-control form-control-sm" placeholder="Search" />
							</div>
						</div>
					</div>

					<div class="col-md-8">
						<div class="form-group">
							<label class="control-label">Apply to Selected Companies</label>
							<?php echo form_dropdown('', $companies, null, 'multiple class="form-control form-control-sm Selectize" id="Companies"') ?>
						</div>
					</div>
					
					
				</div>
				<div class="row">
					<div class="col-md-10">
						<div class="form-group">
							<label class="control-label">Apply to Selected Users</label>
							<select multiple class="form-control form-control-sm Selectize" id="Users">
								<?php foreach ($users as $u) {
									echo '<option value="' . $u['id'] . '">' . $u['fullname'] . '</option>';
								} ?>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label"></label>
							<button type="button" class="btn btn-block btn-success btn-sm" id="UpdatePermission"> <i class="fa fa-save"></i> Update</button>
						</div>
					</div>

				<table class="table table-bordered table-striped table-condensed hide" id="FixedHeader">
				<!-- <thead>
				<tr>
					<th class="big" colspan="2"><?php //echo $name ?></th>
					<th colspan="4"><?php //echo $company['code'] ?></th>
				</tr>

				<tr>
					<th width="32px" class="aligncenter"><i class="icon-check"></i></th>
					<th>Content</th>
					<th width="32px" class="align-middle"><i class="fa fa-eye ReadAll"></i></th>
					<th width="32px" class="align-middle"><i class="fa fa-plus CreateAll"></i></th>
					<th width="32px" class="align-middle"><i class="fa fa-edit UpdateAll"></i></th>
					<th width="32px" class="align-middle"><i class="fa fa-trash-alt DeleteAll"></i></th>
				</tr>
				</thead> -->
				</table>

				<table class="table table-bordered table-striped table-condensed" id="Result">
				<thead>
				<tr>
					<th class="big" colspan="2"><span><?php echo $name ?></span> </th>
					<th colspan="4"><?php echo $company['code'] ?></th>
				</tr>

				<tr>
					<th width="32px" class="aligncenter"><i class="icon-check"></i></th>
					<th>Content</th>
					<th width="32px" class="align-middle"><i class="fa fa-eye ReadAll"></i></th>
					<th width="32px" class="align-middle"><i class="fa fa-plus CreateAll"></i></th>
					<th width="32px" class="align-middle"><i class="fa fa-edit UpdateAll"></i></th>
					<th width="32px" class="align-middle"><i class="fa fa-trash-alt DeleteAll"></i></th>
				</tr>
				</thead>

				<tbody>
				<?php 
				$i = 1;
				$style = '';
				$submenu = function ($submenus, $parent_key = false, $parent = false) use (&$submenu) {
					foreach ($submenus as $menu => $items) {
						if (isset($items['link'])) {
							$perm = 0;
							if (isset($items['permissions'])) {
								$perm = $items['permissions'];
							}

							if ($parent_key) {
								$parent_keys = explode(',', $parent_key);
								$name = $parent_keys[0];
								unset($parent_keys[0]);
								if (count($parent_keys) > 0) {
									$name .= '[' . join('][', $parent_keys) . '][nodes][' . $menu . '][permissions]';
								}
								else {
									$name .= '[nodes][' . $menu . '][permissions]';
								}

								echo '<tr>
									<td class="align-middle"><input type="checkbox" class="form-control form-control-sm RowCheck" /></td>
									<td class="align-middle">' . $parent . ' &gt;&gt; <strong>' . $items['name'] . '</strong></td>
									<td class="align-middle"><input type="checkbox" class="form-control form-control-sm Read" name="' . str_replace('/', 'ZZ', $name) . '[R]" value="' . Auth::READ . '" ' . (($perm & Auth::READ) == Auth::READ ? 'checked="checked"' : null) . ' /></td>
									<td class="align-middle"><input type="checkbox" class="form-control form-control-sm Create" name="' . str_replace('/', 'ZZ', $name) . '[C]" value="' . Auth::CREATE . '" ' . (($perm & Auth::CREATE) == Auth::CREATE ? 'checked="checked"' : null) . ' /></td>
									<td class="align-middle"><input type="checkbox" class="form-control form-control-sm Update" name="' . str_replace('/', 'ZZ', $name) . '[U]" value="' . Auth::UPDATE . '" ' . (($perm & Auth::UPDATE) == Auth::UPDATE ? 'checked="checked"' : null) . ' /></td>
									<td class="align-middle"><input type="checkbox" class="form-control form-control-sm Delete" name="' . str_replace('/', 'ZZ', $name) . '[D]" value="' . Auth::DELETE . '" ' . (($perm & Auth::DELETE) == Auth::DELETE ? 'checked="checked"' : null) . ' /></td>
									</tr>';
							}
							else {
								$name = $menu . '[permissions]';
								echo '<tr>
									<td class="align-middle"><input type="checkbox" class="form-control RowCheck" /></td>
									<td class="align-middle"><strong>' . $items['name'] . '</strong></td>

									<td class="align-middle"><input type="checkbox" class="form-control Read" name="' . str_replace('/', 'ZZ', $name) . '[R]" value="' . Auth::READ . '" ' . (($perm & Auth::READ) == Auth::READ ? 'checked="checked"' : null) . ' /></td>

									<td class="align-middle"><input type="checkbox" class="form-control Create" name="' . str_replace('/', 'ZZ', $name) . '[C]" value="' . Auth::CREATE . '" ' . (($perm & Auth::CREATE) == Auth::CREATE ? 'checked="checked"' : null) . ' /></td>

									<td class="align-middle"><input type="checkbox" class="form-control Update" name="' . str_replace('/', 'ZZ', $name) . '[U]" value="' . Auth::UPDATE . '" ' . (($perm & Auth::UPDATE) == Auth::UPDATE ? 'checked="checked"' : null) . ' /></td>

									<td class="align-middle"><input type="checkbox" class="form-control Delete" name="' . str_replace('/', 'ZZ', $name) . '[D]" value="' . Auth::DELETE . '" ' . (($perm & Auth::DELETE) == Auth::DELETE ? 'checked="checked"' : null) . ' /></td>

								</tr>';
							}
						}

						if (isset($items['nodes'])) {
							if ($parent) {
								$submenu($items['nodes'], $parent_key.',nodes,'.$menu, $parent . ' >> ' . $items['name']);
							}
							else
							{
								$submenu($items['nodes'], $menu, $items['name']);
							}
						}
					}
				};
				
				$submenu(array_merge_recursive($permissions, $this->config->item('menus')));
				?>
				</tbody>
				</table>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {
		$('#userSelect2').select2()
		.on("change", function () {
	    	var user_id = $(this).val();
	    	window.location = "<?php echo site_url($this->_clspath.$this->_class) ?>/index/"+user_id;
	    });
	});


function resizeHeader() {
	<?php echo fixedHeaderJS('#Result', '#FixedHeader', 80); ?>
}

$(document).ready(function() {
	resizeHeader();
	
	var stripeTable = function(table) {
		table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
	};
	$("#Result").filterTable({
		callback: function(term, table) { 
			stripeTable(table); 
		},
		inputSelector: '#input-filter'
	});
	stripeTable($('#Result'));

	$('input.RowCheck').on('change', function() {
		var checked = $(this).is(':checked');
		if (checked)
			$(this).parents('tr').children('td').children('input').prop('checked', true);
		else
			$(this).parents('tr').children('td').children('input').prop('checked', false);
	});

	$('#Result').on('click', '.ReadAll', function() {
		var checked = $('.Read:visible:first').is(':checked');
		if(checked)
			$('.Read:visible').prop('checked', false);
		else
			$('.Read:visible').prop('checked', true);
	});
	$('#Result').on('click', '.CreateAll', function() {
		var checked = $('.Create:visible:first').is(':checked');
		if(checked)
			$('.Create:visible').prop('checked', false);
		else
			$('.Create:visible').prop('checked', true);
	});
	$('#Result').on('click', '.UpdateAll', function() {
		var checked = $('.Update:visible:first').is(':checked');
		if(checked)
			$('.Update:visible').prop('checked', false);
		else
			$('.Update:visible').prop('checked', true);
	});
	$('#Result').on('click', '.DeleteAll', function() {
		var checked = $('.Delete:visible:first').is(':checked');
		if(checked)
			$('.Delete:visible').prop('checked', false);
		else
			$('.Delete:visible').prop('checked', true);
	});

	$('#UpdatePermission').on('click', function(){
		var perm = $("#MainForm").serializeJSON();
		$.ajax({
			url: '<?php echo site_url($this->_clspath.$this->_class.'/edit') ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				user_id: <?php echo $user_id ?>,
				company_id: <?php echo $company['id'] ?>,
				companies: $('#Companies').val(),
				users: $('#Users').val(),
				perm: perm,
			},
			success: function(data) {
				if (data.status == 'OK') {
					new PNotify({
						title: 'Permission Saved',
						text: '<i class=\"icon-angle-double-right\"></i> <strong><?php echo $name ?></strong> Permissions Saved Successfully.',
						type: 'success',
						nonblock: {
							nonblock: true,
							nonblock_opacity: .2
						}
					});
				}
			}
		});
	});
});
</script>