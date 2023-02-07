
<div id="modal-site" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php 
			echo form_open($this->_clspath.$this->_class.'/site/'.$id['id']);
			echo form_hidden('party_id', $id['id']);
		?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Add Party Address</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Site Code</label>
							<input type="text" class="form-control form-control-sm" name="code" value="" />
						</div>
					</div>

					<div class="col-md-8">
						<div class="form-group">
							<label class="control-label">Site Name</label>
							<input type="text" class="form-control form-control-sm" name="name" value="" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Create New Site</button>
			</div>
			</form>
		</div>
	</div>
</div>

<?php
$site_link = '';
foreach ($sites as $s)
	$site_link .= '<li' . ($id['id'] == $s['id'] ? ' class="active"' : '') . '>' .  anchor($this->_clspath.$this->_class.'/site/'.$s['party_id'].'/'.$s['id'],  $s['code']) . '</li>';

echo form_open($this->uri->uri_string(), 'id="MainForm"');
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
    		<?php echo anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> Add', 'class="btn btn-xs btn-success"') . ' ' .
			anchor("#modal-site", '<i class="icon-white icon-plus"></i>  Add Site', 'class="btn btn-xs btn-success" data-toggle="modal"') . ' ' .
			anchor("/tracking/traces/track/parties/".$id['id'], '<i class="icon-refresh"></i> Fetch PAN', 'class="btn btn-xs btn-info Popup"') . ' ' .
			anchor("/tracking/dgft/index/".$id['id'], '<i class="icon-refresh"></i> Fetch IEC Detail', 'class="btn btn-xs btn-info"'); ?>
			<?php echo anchor($this->_clspath.$this->_class.'/edit/'.$party_id['party_id'], $page_title) . '</li>' . $site_link ?>
		</div>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Site Code</label>
						<input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" id="Code" />
					</div>
				</div>

				<div class="col-md-8">
					<div class="form-group">
						<label class="control-label">Name</label>
						<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" id="Name" />
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label">Address</label>
				<input type="text" class="form-control form-control-sm" name="address" value="<?php echo $row['address'] ?>" />
			</div>

			<div class="form-group">
				<label class="control-label">City</label>
				<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
					<?php echo form_dropdown('city_id', $this->kaabar->getCities(), $row['city_id'], 'class="SelectizeKaabar" data-placeholder="Choose City Name..."'); ?>
				</div>
			</div>
			<br />

			<table class="table table-condensed table-striped DataEntry">
			<thead>
				<tr>
					<th width="20%">Designation</th>
					<th>Name</th>
					<th width="20%">Mobile</th>
					<th>Email</th>
					<th width="24px" class="aligncenter"><a class="CheckAll"><i class="icon-trashcan"></i></a></th>
				</tr>
			</thead>

			<tbody>
				<?php
				foreach($contacts as $r) {
					echo '<tr>
					<td><input type="text" class="form-control form-control-sm" name="designation['.$r['id'].'][' . $r['id'] . ']" value="' . $r['designation'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm" name="person_name['.$r['id'].'][' . $r['id'] . ']" value="' . $r['person_name'] . '" size="10" /></td>
					<td><input type="text" class="form-control form-control-sm" name="mobile['.$r['id'].'][' . $r['id'] . ']" value="' . $r['mobile'] . '" size="10" /></td>
					<td><input type="text" class="form-control form-control-sm Text ajaxEmail col-md-12" name="email['.$r['id'].'][' . $r['id'] . ']" value="' . $r['email'] . '" size="10" /></td>
					<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
				</tr>';
				}
				?>

				<tr class="TemplateRow">
					<td><input type="text" class="form-control form-control-sm Focus" name="new_designation[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Validate" name="new_person_name[]" value="" size="10" /></td>
					<td><input type="text" class="form-control form-control-sm" name="new_mobile[]" value="" size="10" /></td>
					<td><input type="text" class="form-control form-control-sm Validate" name="new_email[]" value="" size="10" /></td>
					<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></div></td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>

</form>