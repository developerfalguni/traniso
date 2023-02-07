<style>
.mark_deleted { background-color: #fcc; };
</style>

<div ng-app="stuffingApp">
	<div class="row" ng-controller="stuffingCtrl">
		<div class="col-md-6">
			<div class="card card-default">
				<div class="card-header">
					<span class="card--links">
						<button type="button" class="btn btn-xs btn-danger" ng-click="deleteStuffings()">Delete All Marked</button>
					</span>
					<h3 class="card-title"><?php echo $page_title ?> List</h3>
				</div>
			
				<div class="list-group" id="DivFrame" style="overflow-x: hidden">
					<a href="#" class="list-group-item" ng-repeat="stuffing in stuffings track by $index" ng-click="loadStuffing($index)" ng-class="{'mark_deleted': stuffing.delete_id > 0}">
						<div class="row">
							<div class="col-md-1 big grayLight" ng-bind="$index+1"></div>
							<div class="col-md-5 big">
								<span ng-bind="stuffing.container_no" ng-class="{ 'red': stuffing.container_id == 0 }"></span>
								<i class="purple icon" ng-class="{ 'icon-attachment2': stuffing.job_invoice_id.length > 0 && stuffing.job_invoice_id[0] != '0'}"></i>
							</div>
							<div class="col-md-4 tiny">
								<span ng-bind="stuffing.seal_no" class="blue"></span><br />
								<span ng-bind="stuffing.excise_seal_no" class="blue"></span>
							</div>
							<div class="col-md-2"><span ng-bind="stuffing.container_type"></span> <span class="pull-right" ng-click="deleteStuffing($index)"><i class="icon icon-trashcan" ng-class="{'red': stuffing.delete_id > 0}"></i></span></div>
						</div>

						<div class="row">
							<div class="col-md-2 tiny"><span ng-bind="stuffing.lr_no"></span><br />
								<span ng-bind="stuffing.vehicle_no"></span></div>
							<div class="col-md-4 tiny">
								<span ng-bind="stuffing.pickup_date"></span><br />
								<span ng-bind="stuffing.stuffing_date" class="orange"></span>
							</div>
							<div class="col-md-1 tiny"><span ng-bind="stuffing.units"></span><br />
								<span ng-bind="stuffing.unit"></span></div>
							<div class="col-md-1 tiny"><span ng-bind="stuffing.gross_weight"></span><br />
								<span ng-bind="stuffing.nett_weight"></span></div>
							<div class="col-md-1 tiny">
								<i class="pull-right big icon" ng-class="{ 'icon-pencil': $index == selectedIndex, 'green icon-floppy': $index == lastSaved }"></i>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<form role="form" ng-submit="saveStuffing()">
				<input type="hidden" name="index" value="" id="index" />
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
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Invoice No</label><br />
									<?php echo form_dropdown('job_invoice_id[]', $invoices, 0, 'multiple class="Selectize" id="job_invoice_id"') ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">LR No</label>
									<input type="text" class="form-control form-control-sm" name="lr_no" value="" id="lr_no" />
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vehicle No</label>
									<input type="text" class="form-control form-control-sm" name="vehicle_no" value="" id="vehicle_no" />
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">Container Type</label>
									<?php echo form_dropdown('container_type_id', getSelectOptions('container_types', 'id', 'CONCAT(size, "x", code)'), 0, 'class="form-control form-control-sm" id="container_type_id"') ?>
								</div>
							</div>

							<div class="col-md-8">
								<div class="form-group">
									<label class="control-label">Container No</label>
									<input type="text" class="form-control form-control-sm" name="container_no" value="" id="container_no" />
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Line Seal No</label>
									<input type="text" class="form-control form-control-sm" name="seal_no" value="" id="seal_no" />
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Ex/Cu Seal No</label>
									<input type="text" class="form-control form-control-sm" name="excise_seal_no" value="" id="excise_seal_no" />
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Pickup Date</label>
									<div class="input-group date DateTimePicker">
										<span class="input-group-addon"><i class="icon-calendar"></i></span>
										<input type="text" class="form-control form-control-sm AutoDate" name="pickup_date" value="" id="pickup_date" />
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Stuffing Date</label>
									<div class="input-group date DatePicker">
										<span class="input-group-addon"><i class="icon-calendar"></i></span>
										<input type="text" class="form-control form-control-sm AutoDate" name="stuffing_date" value="" id="stuffing_date" />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Units</label>
									<input type="text" class="form-control form-control-sm Numeric" name="units" value="" id="units" />
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Unit</label>
									<?php echo form_dropdown('unit_id', getSelectOptions('units', 'id', 'code'), 0, 'class="form-control form-control-sm" id="unit_id"') ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Gross Weight</label>
									<input type="text" class="form-control form-control-sm Numeric" name="gross_weight" value="" id="gross_weight" />
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Nett Weight</label>
									<input type="text" class="form-control form-control-sm Numeric" name="nett_weight" value="" id="nett_weight" />
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Remarks</label>
									<input type="text" class="form-control form-control-sm" name="remarks" value="" id="remarks" />
								</div>
							</div>
						</div>
					</div>
				
					<div class="card-footer">
						<button type="submit" class="btn btn-success">Update</button>
					</div>
				</div>
				
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
function resizeDivFrame() {
	$("#DivFrame").height($(window).height()-300);
	// $("#DivFrame").width($(window).width()-30);
}
$(document).ready(function() {
	$(window).resize(function() {
		resizeDivFrame();
	});
	resizeDivFrame();

	$('#container_no').on('keyup', function() {
		if (ISO6346Check($(this).val())) 
			$(this).removeClass('red');
		else
			$(this).addClass('red');
	});
});


/* Angular Stuff */
var stuffingApp = angular.module("stuffingApp", []);

stuffingApp.controller("stuffingCtrl", function($scope) {
	$scope.selectedIndex = -1;
	$scope.lastSaved = -1;
	$scope.stuffings = [
		<?php 
		foreach ($rows['stuffing'] as $r) {
			echo "
				{
					'id': " . $r['id'] . ",
					'job_id': " . $r['id'] . ",
					'job_invoice_id': " . json_encode(explode(',', $r['job_invoice_id'])) . ",
					'lr_no': '" . $r['lr_no'] . "',
					'vehicle_no': '" . $r['vehicle_no'] . "',
					'container_type_id': " . $r['container_type_id'] . ",
					'container_no': '" . $r['container_no'] . "',
					'seal_no': '" . $r['seal_no'] . "',
					'excise_seal_no': '" . $r['excise_seal_no'] . "',
					'pickup_date': '" . $r['pickup_date'] . "',
					'stuffing_date': '" . $r['stuffing_date'] . "',
					'units': '" . $r['units'] . "',
					'unit': '" . $r['unit'] . "',
					'unit_id': '" . $r['unit_id'] . "',
					'gross_weight': '" . $r['gross_weight'] . "',
					'nett_weight': '" . $r['nett_weight'] . "',
					'flexi_tank_no': '" . $r['flexi_tank_no'] . "',
					'driver_contact_no': '" . $r['driver_contact_no'] . "',
					'container_id': '" . $r['container_id'] . "',
					'container_type': '" . $r['container_type'] . "',
					'remarks': '" . $r['remarks'] . "',
				},
				";
		}

		foreach ($rows['containers'] as $container_type_id => $container_type) {
			for($index = 0; $index < $container_type['count']; $index++) {
				echo "
				{
					'id': 0,
					'job_id': " . $job_id['id'] . ",
					'job_invoice_id': '',
					'lr_no': '',
					'vehicle_no': '',
					'container_type_id': " . $container_type_id . ",
					'container_no': '',
					'seal_no': '',
					'excise_seal_no': '',
					'pickup_date': '',
					'stuffing_date': '" . (isset($container_type['stuffing_date']) ? substr($container_type['stuffing_date'], 0, 10) : '') . "',
					'units': 0,
					'unit_id': 0,
					'gross_weight': 0,
					'nett_weight': 0,
					'flexi_tank_no': '',
					'driver_contact_no': '',
					'container_id': 0,
					'container_type': '" . $container_type['container_type'] . "',
					'remarks': '',
				},
				";
			}
		}
		?>
	];
	$scope.loadStuffing = function(index) {
		$scope.selectedIndex = index;
		$('#index').val(index);
		
		var $select = $('#job_invoice_id').selectize();
		var selectize = $select[0].selectize;
		selectize.refreshItems();
		selectize.setValue($scope.stuffings[index].job_invoice_id);
		// $('#job_invoice_id').val($scope.stuffings[index].job_invoice_id);
		
		$('#lr_no').val($scope.stuffings[index].lr_no);
		$('#vehicle_no').val($scope.stuffings[index].vehicle_no);
		$('#container_type_id').val($scope.stuffings[index].container_type_id);
		$('#container_no').val($scope.stuffings[index].container_no);
		$('#seal_no').val($scope.stuffings[index].seal_no);
		$('#excise_seal_no').val($scope.stuffings[index].excise_seal_no);
		$('#pickup_date').val($scope.stuffings[index].pickup_date);
		$('#stuffing_date').val($scope.stuffings[index].stuffing_date);
		$('#units').val($scope.stuffings[index].units);
		$('#unit_id').val($scope.stuffings[index].unit_id);
		$('#gross_weight').val($scope.stuffings[index].gross_weight);
		$('#nett_weight').val($scope.stuffings[index].nett_weight);
		$('#flexi_tank_no').val($scope.stuffings[index].flexi_tank_no);
		$('#driver_contact_no').val($scope.stuffings[index].driver_contact_no);
		$('#remarks').val($scope.stuffings[index].remarks);

		selectize.focus();
	};
	$scope.saveStuffing = function() {
		index = $('#index').val();
		$scope.lastSaved = index;

		$.ajax({
			url: '<?php echo base_url($this->_clspath.$this->_class.'/saveStuffing') ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				id:                $scope.stuffings[index].id,
				job_id:            $scope.stuffings[index].job_id,
				job_invoice_id: $('#job_invoice_id').val(),
				lr_no:             $('#lr_no').val(),
				vehicle_no:        $('#vehicle_no').val(),
				container_type_id: $('#container_type_id').val(),
				container_no:      $('#container_no').val(),
				seal_no:           $('#seal_no').val(),
				excise_seal_no:    $('#excise_seal_no').val(),
				pickup_date:       $('#pickup_date').val(),
				stuffing_date:     $('#stuffing_date').val(),
				units:             $('#units').val(),
				unit_id:           $('#unit_id').val(),
				gross_weight:      $('#gross_weight').val(),
				nett_weight:       $('#nett_weight').val(),
				flexi_tank_no:     $('#flexi_tank_no').val(),
				driver_contact_no:  $('#driver_contact_no').val(),
				container_id:      $('#container_id').val(),
				container_type:    $('#container_type').val(),
				remarks:           $('#remarks').val(),
			},
			success: function(json) {
				$scope.$apply(function(){
				    $scope.stuffings[index].id                = json.id;
				    $scope.stuffings[index].job_invoice_id = json.job_invoice_id;
					$scope.stuffings[index].lr_no             = json.lr_no;
					$scope.stuffings[index].vehicle_no        = json.vehicle_no;
					$scope.stuffings[index].container_type_id = json.container_type_id;
					$scope.stuffings[index].container_no      = json.container_no;
					$scope.stuffings[index].seal_no           = json.seal_no;
					$scope.stuffings[index].excise_seal_no    = json.excise_seal_no;
					$scope.stuffings[index].pickup_date       = json.pickup_date;
					$scope.stuffings[index].stuffing_date     = json.stuffing_date;
					$scope.stuffings[index].units             = json.units;
					$scope.stuffings[index].unit_id           = json.unit_id;
					$scope.stuffings[index].gross_weight      = json.gross_weight;
					$scope.stuffings[index].nett_weight       = json.nett_weight;
					$scope.stuffings[index].flexi_tank_no     = json.flexi_tank_no;
					$scope.stuffings[index].driver_contact_no = json.driver_contact_no;
					$scope.stuffings[index].container_id      = json.container_id;
					$scope.stuffings[index].container_type    = json.container_type;
					$scope.stuffings[index].remarks           = json.remarks;

					new PNotify({
						title: 'Saved',
						text: '<i class="icon-angle-double-right"></i> Stuffing Saved Successfully.',
						type: 'success',
						nonblock: {
							nonblock: true,
							nonblock_opacity: .2
						}
					});

					index++;
					if ($scope.stuffings.length <= index) {
						index = 0;
						$scope.loadStuffing(index);
					}
				});
			}
		});
	};
	$scope.deleteStuffing = function(index) {
		if ($scope.stuffings[index].delete_id == 0)
			$scope.stuffings[index].delete_id = $scope.stuffings[index].id;
		else
			$scope.stuffings[index].delete_id = 0;
	};

	$scope.deleteStuffings = function() {
		deleted_ids = [];
		$.each($scope.stuffings, function(i, item){
			if (item.delete_id > 0)
				deleted_ids.push(item.delete_id);
		});
		var form = '';
        $.each(deleted_ids, function(key, value) {
            form += '<input type="hidden" name="delete_id[]" value="'+value+'">';
        });
        $('<form action="<?php echo base_url($this->_clspath.$this->_class.'/delete') ?>" method="POST">'+form+'</form>').appendTo('body').submit();
	};
});
</script>