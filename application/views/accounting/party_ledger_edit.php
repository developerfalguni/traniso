
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class."/delete/".$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
echo form_hidden('reference_ledger_id', $row['reference_ledger_id']);
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
		<div class="row">
			<div class="col-md-6">
				<fieldset>
					<div class="form-group<?php echo (strlen(form_error('party_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Choose Party</label>
						<input type="hidden" name="party_id" value="<?php echo $row['party_id'] ?>" id="PartyID" />
						<input type="text" class="form-control form-control-sm Focus" value="<?php echo $party_name ?>" id="ajaxParty" />
					</div>

					<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Code</label>
						
							<input type="text" class="form-control form-control-sm input-small" name="code" value="<?php echo $row['code'] ?>" maxlength="10" id="Code" />
						
					</div>
					
					<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Name</label>
						
							<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
						
					</div>

					<div class="form-group<?php echo (strlen(form_error('category')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Category</label>
						
							<?php echo form_dropdown('category', $categories, $row['category'], 'class="form-control form-control-sm"') ?>
						
					</div>

					<div class="form-group<?php echo (strlen(form_error('account_group_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Accounting Head</label>
						
							<?php echo form_dropdown('account_group_id', getSelectOptions('account_groups', 'id', 'name'), $row['account_group_id'], 'class="form-control form-control-sm"') ?>
						
					</div>

					<div class="form-group<?php echo (strlen(form_error('group_name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Group Name</label>
						
							<input type="text" class="form-control form-control-sm" name="group_name" value="<?php echo $row['group_name'] ?>" id="GroupName" />
						
					</div>
					
					<div class="form-group<?php echo (strlen(form_error('parent_ledger_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Parent</label>
						
							<input type="hidden" name="parent_ledger_id" value="<?php echo $row['parent_ledger_id'] ?>" id="Parent_ID" />
							<input type="text" class="form-control form-control-sm" value="<?php echo $parent_code_name ?>" id="ajaxParent" />
						
					</div>
					
					<div class="form-group<?php echo (strlen(form_error('opening_balance')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Opening Balance</label>
						<div class="form-inline">
							<input type="text" class="form-control form-control-sm Numeric" name="opening_balance" value="<?php echo $row['opening_balance'] ?>" />
							<?php echo form_dropdown('dr_cr', getEnumSetOptions($this->_table, 'dr_cr'), $row['dr_cr'], 'class="form-control form-control-sm"') ?>
						</div>
					</div>

					<div class="form-group<?php echo (strlen(form_error('tds_class_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">TDS Deductee Class</label>
						
							<?php echo form_dropdown('tds_class_id', array(0=>'')+getSelectOptions('tds_classes', 'id', 'name', 'WHERE type = "Deductee"'), $row['tds_class_id'], 'class="form-control form-control-sm"') ?>
						
					</div>
				</fieldset>
			</div>

			<div class="col-md-6">
				<fieldset>
					<legend>
					<div class="row">
						<div class="col-md-8">Collection Responsibility</div>
						<div class="col-md-4">
							<label><input type="radio" name="save_to" value="S" checked="true" /> <span class="label label-info">Ledger</span></label>
							<label><input type="radio" name="save_to" value="G" /> <span class="label label-info">Group</span></div></label>
					</div>
				</legend>	
					<div class="form-group">
						<label class="control-label">Monitoring Responsibility</label>
						<input type="hidden" name="monitoring_id" value="<?php echo $row['monitoring_id'] ?>" id="MonitoringID" />
						<input type="text" class="form-control form-control-sm" name="monitoring_name" value="<?php echo $monitoring_name ?>" id="MonitoringName" /> 
					</div>

					<div class="form-group">
						<label class="control-label">Finalizing Responsibility 1</label>
						<input type="hidden" name="finalizing1_id" value="<?php echo $row['finalizing1_id'] ?>" id="Finalizing1ID" />
						<input type="text" class="form-control form-control-sm" name="finalizing1_name" value="<?php echo $finalizing1_name ?>" id="Finalizing1Name" /> 
					</div>

					<div class="form-group">
						<label class="control-label">Finalizing Responsibility 2</label>
						<input type="hidden" name="finalizing2_id" value="<?php echo $row['finalizing2_id'] ?>" id="Finalizing2ID" />
						<input type="text" class="form-control form-control-sm" name="finalizing2_name" value="<?php echo $finalizing2_name ?>" id="Finalizing2Name" /> 
					</div>
				</fieldset>
			</div>
		</div>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$("#GroupName").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/ledgers/group_name') ?>',
		minLength: 0
	});

	$("#ajaxParty").autocomplete({
		source: '<?php echo site_url('master/party/ajax') ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#ajaxParty").val( ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$("#ajaxParty").val(ui.item.name);
			$("#PartyID").val(ui.item.id);
	
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#ajaxParty").val('');
				$("#PartyID").val(0);
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append("<a>" + item.name + "</a>")
			.appendTo(ul);
	};

	$("#ajaxParent").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxParent/' . $category . '/'.$id['id']) ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#Parent_ID").val(ui.item.id);
			$("#ajaxParent").val(ui.item.code + " - " + ui.item.name);
			$("#GroupName").val(ui.item.group_name);
			return false;
		},
		select: function(event, ui) {
			$("#Parent_ID").val(ui.item.id);
			$("#ajaxParent").val(ui.item.code + " - " + ui.item.name);
			$("#GroupName").val(ui.item.group_name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#Parent_ID").val(0);
                $("#ajaxParent").val('');
                $("#GroupName").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</a>')
			.appendTo(ul);
	};

	$('.DataEntry').on('keydown.autocomplete', '.fa_Code', function(event, items) {
		var id    = $(this).prevAll('input');
		var opbal = $(this).parent('td').parent('tr').find('.OpeningBalance');
		var drcr  = $(this).parent('td').parent('tr').find('.fa_DRCR');
		$(this).autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxChild/' . $category . '/'.$id['id'].'/0') ?>',
			minLength: 0,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code);
				$(opbal).val(ui.item.opening_balance);
				$(drcr).val(ui.item.dr_cr);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code);
				$(opbal).val(ui.item.opening_balance);
				$(drcr).val(ui.item.dr_cr);
				return false;
			},
			response: function(event, ui) {
	            if (ui.content.length === 0) {
					$(id).val(0);
					$(this).val('');
					$(opbal).val(0);
					$(drcr).val('Dr');
	            }
	        }
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</a>')
				.appendTo(ul);
		};
	});

	$('.DataEntry').on('keydown.autocomplete', '.Reference', function(event, items) {
		var id = $(this).prevAll('input');
		$(this).autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxLedgers/' . $category) ?>',
			minLength: 2,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code + ' - ' + ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code + ' - ' + ui.item.name);
				return false;
			},
			response: function(event, ui) {
	            if (ui.content.length === 0) {
					$(id).val(0);
	                $(this).val('');
	            }
	        }
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</a>')
				.appendTo(ul);
		};
	});

	$("#MonitoringName").autocomplete({
		source: '<?php echo site_url('/master/staff/ajax') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$("#MonitoringID").val(ui.item.id);
			$("#MonitoringName").val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$("#MonitoringID").val(ui.item.id);
			$("#MonitoringName").val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#MonitoringID").val(0);
                $("#MonitoringName").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + '</a>')
			.appendTo(ul);
	};

	$("#Finalizing1Name").autocomplete({
		source: '<?php echo site_url('/master/staff/ajax') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$("#Finalizing1ID").val(ui.item.id);
			$("#Finalizing1Name").val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$("#Finalizing1ID").val(ui.item.id);
			$("#Finalizing1Name").val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#Finalizing1ID").val(0);
                $("#Finalizing1Name").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + '</a>')
			.appendTo(ul);
	};

	$("#Finalizing2Name").autocomplete({
		source: '<?php echo site_url('/master/staff/ajax') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$("#Finalizing2ID").val(ui.item.id);
			$("#Finalizing2Name").val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$("#Finalizing2ID").val(ui.item.id);
			$("#Finalizing2Name").val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#Finalizing2ID").val(0);
                $("#Finalizing2Name").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + '</a>')
			.appendTo(ul);
	};
});
</script>