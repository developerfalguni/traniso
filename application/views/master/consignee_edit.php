<div id="modal-add" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php 
			echo form_open($this->_clspath.$this->_class.'/createAddress');
			echo form_hidden('consignee_id', $id['id']);
		?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Add Address</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="control-label">Address Code</label>
						<input type="text" class="form-control form-control-sm" name="new_code" value="" />
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Create New Address</button>
			</div>
		</form>
		</div>
	</div>
</div>

<?php
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
		</div>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="row">
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('vi_code')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Code</label>
						<input type="text" class="form-control form-control-sm Focus" name="vi_code" value="<?php echo $row['vi_code'] ?>" maxlength="5" id="Code" />
					</div>
				</div>

				<div class="col-md-10">
					<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Name</label>
						<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
					</div>
				</div>
			</div>

			<div class="tabbable">
				<ul class="nav nav-tabs" id="Addresses">
					<?php
						foreach ($addresses as $r) {
							echo '<li><a href="#Address_' . $r['id'] . '" Address_id=' . $r['id'] . '>' . $r['vi_code'] . '</a></li>';
						}

						if ($id['id'] > 0)
							echo '<li class="pull-right"><a href="#modal-add" data-toggle="modal" class="AddBranch"><i class="fa fa-plus"></i> Add Branch</a></li>';
					?>
				</ul>

				<div class="tab-content">
					<?php foreach ($addresses as $r) : ?> 
						
					<div class="tab-pane pane-box" id="Address_<?php echo $r['id'] ?>">
						<fieldset>
							<div class="row">
								<div class="col-md-2">
									<div class="form-group<?php echo (strlen(form_error('vi_code')) > 0 ? ' has-error' : '') ?>">
										<label class="control-label">Address Code</label>
										
											<input type="text" class="form-control form-control-sm" name="address_vi_code[<?php echo $r['id'] ?>]" value="<?php echo $r['vi_code'] ?>"  />
										
									</div>
								</div>

								<div class="col-md-10">
									<div class="form-group">
										<label class="control-label">Address</label>
										
											<input type="text" class="form-control form-control-sm" name="address[<?php echo $r['id'] ?>]" value="<?php echo $r['address'] ?>"  />
										
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">City</label>
										
											<input type="text" class="form-control form-control-sm" name="city[<?php echo $r['id'] ?>]" value="<?php echo $r['city'] ?>"  />
										
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Country</label>
										
											<input type="text" class="form-control form-control-sm Country" name="country[<?php echo $r['id'] ?>]" value="<?php echo $r['country'] ?>"  />
										
									</div>
								</div>
							</div>
						
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Contact</label>
										
											<input type="text" class="form-control form-control-sm" name="contact[<?php echo $r['id'] ?>]" value="<?php echo $r['contact'] ?>" />
										
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Email</label>
										
											<input type="text" class="form-control form-control-sm" name="email[<?php echo $r['id'] ?>]" value="<?php echo $r['email'] ?>"/>
										
									</div>
								</div>
							</div>
						</fieldset>
					</div>

					<?php endforeach; ?>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
</form>

<script language="JavaScript">
$(document).ready(function() {
	$('#Addresses a:first').tab('show');

	$('#Addresses a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	})

	$('.Country').on('keydown.autocomplete', function(event, items) {
		$(this).autocomplete({
			source: '<?php echo site_url('master/country/ajax') ?>',
			minLength: 0,
			focus: function(event, ui) {
				$(this).val(ui.item.code + ' - ' + ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(this).val(ui.item.code + ' - ' + ui.item.name);
				return false;
			},
			response: function(event, ui) {
	            if (ui.content.length === 0) {
	                $(this).val('');
	            }
	        }
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append("<a>" + item.name + " <span class='tiny orange'>" + item.code + "</span></a>")
				.appendTo(ul);
		};
	});
});

</script>
