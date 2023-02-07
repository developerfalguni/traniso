
<?php
	echo form_open($this->uri->uri_string(), 'id="MainForm"');
	echo form_hidden($id);
	echo form_hidden(array('cargo_type' => $row['cargo_type']));
	echo form_hidden(array('be_type' => $row['be_type']));
?>
	<div class="card-body">
		<div class="row">
			<!-- need to discuss -->
			<div class="col-md-3 d-none">
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">BL 1</label>
							<input type="text" class="form-control form-control-sm" name="bl_no1" value="<?php echo $row['bl_no1'] ?>" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">BL 2</label>
							<input type="text" class="form-control form-control-sm" name="bl_no2" value="<?php echo $row['bl_no2'] ?>" />
						</div>
					</div>
					<div class="col-md-2 d-none">
						<div class="form-group">
							<label class="control-label">House BL</label><br>
							<div class="icheck-primary d-inline">
								<?php echo form_checkbox(array('name' => 'house_bl', 'id' => 'checkboxPrimary3', 'value' => 'Yes', 'checked' => ($row['house_bl'] == 'Yes' ? true : false))) ?>
								<label for="control-label">
								  	Yes
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-1 d-none">
						<div class="form-group">
							<label class="control-label">Coastal</label><br />
							<div class="icheck-primary d-inline">
								<?php echo form_checkbox(array('name' => 'is_coastal', 'id' => 'checkboxPrimary3', 'value' => 'Yes', 'checked' => ($row['is_coastal'] == 'Yes' ? true : false))) ?>
								<label for="checkboxPrimary3">
								  Yes
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end need to discuss -->
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Job Services <i class="text-danger">*</i> </label>
					<?php echo form_dropdown('sub_type[]', getEnumSetOptions('jobs', 'sub_type'), explode(",", $row['sub_type']), 'multiple class="Selectize"'); ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Branch</label>
					<?php echo form_dropdown('branch_id', getSelectOptions('branches', 'id', 'name', 'where company_id = '.$company_id), $row['branch_id'], 'class="SelectizeKaabar"') ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Job No</label>
					<input type="text" class="form-control form-control-sm" value="<?php echo $row['id2_format']; ?>" readonly="true" />
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group <?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control DatePicker" name="date" value="<?php echo $row['date']; ?>" id="Date" maxlength="10" />
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Job Reference No</label>
					<input type="text" class="form-control form-control-sm" value="<?php echo $row['id2_format']; ?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Cargo Type</label>
					<?php echo form_dropdown('cargo_type', $this->import->getCargoTypes(), $row['cargo_type'], 'class="form-control form-control-sm SelectizeKaabar" id="CargoType"') ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Shipment Type</label>
					<?php echo form_dropdown('shipment_type', $this->import->getContainerShipmentTypes(), $row['shipment_type'], 'class="form-control form-control-sm" id="ajaxShipmentType"') ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Inco Terms</label>
					<?php echo form_dropdown('incoterms', $this->import->getIncoTerms(), $row['incoterms'], 'class="form-control form-control-sm SelectizeKaabar" id="incoTerms"') ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Freight Terms</label>
					<?php echo form_dropdown('freight_terms', $this->import->getFreightTerms(), $row['freight_terms'], 'class="form-control form-control-sm SelectizeKaabar" id="freightTerms"') ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Delivery Type</label>
					<?php echo form_dropdown('delivery_type', $this->import->getDeliveryTypes(), $row['delivery_type'], 'class="form-control form-control-sm SelectizeKaabar" id="deliveryType"') ?>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('party_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Billing Party Name</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="party_name" value="<?php echo $party_name ?>" size="30" id="ajaxParty">
						<input type="hidden" name="party_id" value="<?php echo $row['party_id'] ?>" id="PartyID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('shipper_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Shipper Name</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="shipper_name" value="<?php echo $shipper_name ?>" id="ajaxShipper" />
						<input type="hidden" name="shipper_id" value="<?php echo $row['shipper_id'] ?>" id="ShipperID" />
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('importer1_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Importer 1</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="importer1_name" value="<?php echo $importer1_name ?>" id="ajaxImporter1" />
						<input type="hidden" name="importer1_id" value="<?php echo $row['importer1_id'] ?>" id="Importer1ID" />
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('importer2_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Importer 2</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="importer2_name" value="<?php echo $importer2_name ?>" id="ajaxImporter2" />
						<input type="hidden" name="importer2_id" value="<?php echo $row['importer2_id'] ?>" id="Importer2ID" />
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('origin_agent_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Origin Agent</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="origin_agent_name" value="<?php echo $origin_agent_name ?>" size="30" id="ajaxOriginAgent">
						<input type="hidden" name="origin_agent_id" value="<?php echo $row['origin_agent_id'] ?>" id="OriginAgentID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('forwarder_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Forwarder</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="forwarder_name" value="<?php echo $forwarder_name ?>" id="ajaxforwarder" />
						<input type="hidden" name="forwarder_id" value="<?php echo $row['forwarder_id'] ?>" id="forwarderID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('line_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Shipping Line</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm" name="line_name" value="<?php echo $line_name ?>" id="ajaxLine" />
						<input type="hidden" name="line_id" value="<?php echo $row['line_id'] ?>" id="LineID" />
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('cha_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">CHA</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="cha_name" value="<?php echo $cha_name ?>" id="ajaxCHA">
						<input type="hidden" name="cha_id" value="<?php echo $row['cha_id'] ?>" id="CHAID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('shipment_port_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">POL (Port of Loading)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="shipment_port" value="<?php echo $shipment_port ?>" id="ajaxShipmentPort">
						<input type="hidden" name="shipment_port_id" value="<?php echo $row['shipment_port_id'] ?>" id="ShipmentPortID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('origin_country_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Country of Origin</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" value="<?php echo $origin_country ?>" id="ajaxOCountry">
						<input type="hidden" name="origin_country_id" value="<?php echo $row['origin_country_id'] ?>" id="OriginCountryID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('discharge_port_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">POD (Dischrge Port)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="discharge_port" value="<?php echo $discharge_port ?>" id="ajaxDischargePort">
						<input type="hidden" name="discharge_port_id" value="<?php echo $row['discharge_port_id'] ?>" id="DischargePortID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('fpod_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">FPOD (Destination Port)</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="destination_port" value="<?php echo $destination_port ?>" id="ajaxDestinationPort">
						<input type="hidden" name="fpod_id" value="<?php echo $row['fpod_id'] ?>" id="DestinationPortID">
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('fpod_eta')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">ETA Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="fpod_eta" value="<?php echo $row['fpod_eta']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('fpod_etd')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">ETD Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="fpod_etd" value="<?php echo $row['fpod_etd']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('product_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Commodity</label>
					<?php echo form_dropdown('product_id', array(0=>'')+getSelectOptions('products'), $row['product_id'], 'class="SelectizeKaabar"') ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('net_weight')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Net Weight</label>
							<input type="text" class="form-control form-control-sm Numeric" onkeypress="return isNumberDot(event)" name="net_weight" value="<?php echo $row['net_weight'] ?>" size="12" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('net_weight_unit')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Unit</label>
							<?php echo form_dropdown('net_weight_unit', getSelectOptions('units', 'code', 'code'), $row['net_weight_unit'], 'class="SelectizeKaabar"'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('gross_weight')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Gross Weight</label>
							<input type="text" class="form-control form-control-sm Numeric" onkeypress="return isNumberDot(event)" name="gross_weight" value="<?php echo $row['gross_weight'] ?>" size="12" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('net_weight_unit')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Unit</label>
							<?php echo form_dropdown('gross_weight_unit', getSelectOptions('units', 'code', 'code'), $row['gross_weight_unit'], 'class="form-control form-control-sm SelectizeKaabar"'); ?>
				
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('invoice_no')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Invoice No</label>
							<input type="text" class="form-control form-control-sm" name="invoice_no" value="<?php echo $row['invoice_no']; ?>" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('invoice_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Invoice Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="invoice_date" value="<?php echo $row['invoice_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Package Description</label>
					<div class="form-group<?php echo (strlen(form_error('details')) > 0 ? ' has-error' : '') ?>">
						<input type="text" class="form-control form-control-sm" name="details" value="<?php echo $row['details'] ?>" />
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('packages')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">No. Packages</label>
							<input type="text" class="form-control form-control-sm Numeric" name="packages" value="<?php echo $row['packages'] ?>" size="12" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('package_type_id')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Unit</label>
							<?php echo form_dropdown('package_type_id', getSelectOptions('package_types', 'id', 'name'), $row['package_type_id'], 'class="form-control form-control-sm SelectizeKaabar"'); ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('invoice_types')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Invoice Type</label>
							<?php echo form_dropdown('invoice_types', $this->import->getInvoiceTypes(), $row['invoice_types'], 'class="form-control form-control-sm SelectizeKaabar"'); ?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('hsn_code')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">HSN Code</label>
							<input type="text" class="form-control form-control-sm" onkeypress="return isNumber(event)" maxlength="8" name="hsn_code" value="<?php echo $row['hsn_code'] ?>" id="hsnCode">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">

			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('cfs_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">CFS Name</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" name="cfs_name" value="<?php echo $cfs_name ?>" id="ajaxCFS">
						<input type="hidden" name="cfs_id" value="<?php echo $row['cfs_id'] ?>" id="CFSID" />
						<span class="input-group-append">
							<a href="#" class="btn btn-info"><i class="fa fa-plus"></i></a>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Sales Person</label>
					<?php echo form_dropdown('salesman', getSelectOptions('staffs', 'id', 'CONCAT(firstname," ", lastname)'), $row['salesman'], 'class="form-control form-control-sm SelectizeKaabar" id="salesMan"') ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Handled By</label>
					<?php echo form_dropdown('handled_by', getSelectOptions('staffs', 'id', 'CONCAT(firstname," ", lastname)'), $row['handled_by'], 'class="form-control form-control-sm SelectizeKaabar" id="handledBy"') ?>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('transit_days')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Transit Days</label>
					<input type="text" class="form-control form-control-sm Numeric" name="transit_days" value="<?php echo $row['transit_days'] ?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-5">
						<div class="form-group">
							<label class="control-label">Free Days</label>
							<input type="text" class="form-control form-control-sm Numeric" name="free_days" value="<?php echo $row['free_days'] ?>">
						</div>
					</div>
					<div class="col-md-7">
						<div class="form-group">
							<label class="control-label">Free Days Upto</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="free_days_upto" value="<?php echo $row['free_days_upto']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('bl_no')) > 0 ? ' error' : '')  ?>">
							<label class="control-label">BL No</label>
							<input type="text" class="form-control form-control-sm" name="bl_no" value="<?php echo $row['bl_no']; ?>" id="BLNo" onchange="javascript: checkDuplicateBL()" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('bl_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">BL Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="bl_date" value="<?php echo $row['bl_date']; ?>" id="BLDate" onchange="javascript: checkDuplicateBL()" />
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('be_no')) > 0 ? ' error' : '')  ?>">
							<label class="control-label">BE No</label>
							<input type="text" class="form-control form-control-sm" name="be_no" value="<?php echo $row['be_no']; ?>" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('be_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">BE Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="be_date" value="<?php echo $row['be_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('original_bl_received')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">OG Doc Rcvd</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="original_bl_received" value="<?php echo $import_details['original_bl_received']; ?>" />
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('copy_bl_received')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Copy Doc Rcvd</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="copy_bl_received" value="<?php echo $import_details['copy_bl_received']; ?>" />
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Container 20</label>
							<input type="text" class="form-control form-control-sm" name="container_20" onkeypress="return isNumber(event)" value="<?php echo $row['container_20'] ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Container 40</label>
							<input type="text" class="form-control form-control-sm" name="container_40" onkeypress="return isNumber(event)"value="<?php echo $row['container_40'] ?>">
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Status</label>
					<?php echo form_dropdown('status', $this->import->getStatus(), $row['status'], 'class="form-control form-control-sm SelectizeKaabar"'.(($job['lock'])?' disabled="disabled"':'') ); ?>
				</div>
			</div>
			<div class="col-md-3">
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Remarks</label>
					<div class="form-group">
						<textarea class="form-control" rows="3" name="remarks"><?php echo $import_details['remarks'] ?></textarea>
					</div>
				</div>
			</div>

			
		</div>
		
		<div class="row">	
			<div class="col-md-6 d-none">
				<div class="form-group">
					<label class="control-label">Final Vessel Name</label>
					<div class="form-group <?php echo (strlen(form_error('vessel_id')) > 0 ? ' has-error' : '') ?>">
						<input type="hidden" name="vessel_id" value="<?php echo $row['vessel_id'] ?>" id="VesselID" />
						<input type="text" class="form-control form-control-sm" name="vessel_name" value="<?php echo $vessel_name ?>" size="30" id="ajaxVessel" />
					</div>
				</div>
			</div>
		</div>
		<div class="row d-none">
			<div class="col-md-4">
				<table class="table table-condensed table-striped DataEntry1" id="HighSeas">
				<thead>
				<tr>
					<th width="24px">No</th>
					<th>HSS Party Name</th>
					<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
				</tr>
				</thead>

				<tbody>
					<?php 
						$i = 1;
						foreach ($hss_parties as $hss) {
							echo '<tr>
					<td>' . $i++ . '</td>
					<td class="tiny orange">' . $hss['name'] . '</td>
					<td class="aligncenter">' . form_checkbox(array('name' => 'hss_delete_id['.$hss['id'].']', 'value' => $hss['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
				</tr>';
						}
					?>

				<tr class="TemplateRow1">
					<td></td>
					<td><input type="hidden" name="new_hss_id[]" value="" />
						<input type="text" class="form-control form-control-sm BlankHSS Validate Focus" value="" />
					<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
				</tbody>
				</table>
			</div>
			<div class="col-md-8">
				<div class="row">
					

					
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Place of Delivery</label>
							<div class="form-group<?php echo (strlen(form_error('place_of_delivery')) > 0 ? ' has-error' : '') ?>">
								<input type="text" class="form-control form-control-sm" name="place_of_delivery" value="<?php echo $import_details['place_of_delivery'] ?>" />
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Temp. Vessel Name</label>
							<div class="form-group<?php echo (strlen(form_error('temp_vessel_name')) > 0 ? ' has-error' : '') ?>">
								<input type="text" class="form-control form-control-sm" name="temp_vessel_name" value="<?php echo $import_details['temp_vessel_name'] ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Remarks</label>
							<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
								<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $import_details['remarks'] ?>"  />
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Status</label>
							<?php echo form_dropdown('status', $this->import->getStatus(), $row['status'], 'class="form-control form-control-sm SelectizeKaabar"'.(($job['lock'])?' disabled="disabled"':'') ); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Website</label><br />
							<?php echo form_checkbox(array('name' => 'website', 'value' => 'Yes', 'checked' => ($row['website'] == 'Yes' ? true : false))) ?> Yes
						</div>
					</div>

					<div class="col-md-10">
						<div class="form-group">
							<label class="control-label">Link to Party</label>
							<input type="hidden" name="web_party_id" value="<?php echo $row['web_party_id'] ?>" id="WebPartyID" />
							<input type="text" class="form-control form-control-sm" value="<?php echo $web_party_name ?>" id="ajaxWebParty" />
						</div>
					</div>
				</div>
			</div>
		</div>

		
	</div>
	<?php if($row['id'] > 0){ ?>
	<div class="card-header pt-0">
		<div class="row">
			
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<button type="button" data-toggle="modal" class="btn btn-info ml-2" onclick="updateContainers('<?php echo $row["id"] ?>')"> <i class="fa fa-truck"></i> Update Containers</button>
				<button type="button" class="btn btn-info ml-2" onclick="updateCostsheet('<?php echo $row["id"] ?>')"> <i class="fa fa-rupee-sign"></i> Update Costsheet</button>
				<button type="button" data-toggle="modal" class="btn btn-info ml-2" onclick="attachDocuments('<?php echo $row["id"] ?>')"> <i class="fa fa-upload"></i> Upload Documents</button>
			</div>
			<div class="col-md-3"></div>
		</div>
	</div>
	<?php } ?>
		
	
	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update"><i class="fa fa-save"></i> Update</button>
		<?php if($row['id'] > 0){ ?>
			<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Delete</button>
		<?php } ?>	
		<a href="<?php echo site_url($this->_clspath.$this->_class) ?>" class="btn btn-secondary float-right"><i class="fa fa-chevron-circle-left"></i> Back</a>
	</div>
</div>
</form>

<?php $this->load->view($this->_clspath.'models') ?>

<script type="text/javascript">
	var hss_checked = 1;

	function CheckAllHSS() {
		if(hss_checked) {
			$("#HighSeas input.DeleteCheckbox").attr("checked", "checked");
			hss_checked = 0;
		} else {
			$("#HighSeas input.DeleteCheckbox").removeAttr("checked");
			hss_checked = 1;
		}
	}

	function checkDuplicateBL() {
		var bl_no = $("#BLNo").val();
		var bl_date = $("#BLDate").val();
		$.get("<?php echo base_url($this->_clspath.$this->_class.'/checkDuplicateBL') ?>/"+bl_no+"/"+bl_date);
	}

	function make_copy_hss(id) {
		var v0 = $("#HighSeas tr#Blank input:eq(0)").val();
		var v1 = $("#HighSeas tr#Blank input:eq(1)").val();

		if (!v0) return;
		
		if (id > 1) {
			$("#HighSeas tr#H_1").clone().insertBefore("#HighSeas tr#Blank").attr("id", "H_"+id);
		}

		$("#HighSeas tr#Blank input").each(function(index) {
			$(this).val("");
		});
		$("#HighSeas tr#Blank td a").attr("href", "javascript:make_copy_hss("+(id+1)+")");

		$("#HighSeas tr#H_"+id+" input:eq(0)").val(v0);
		$("#HighSeas tr#H_"+id+" input:eq(1)").val(v1);
		$("#HighSeas tr#H_"+id+" td a").attr("href", "javascript:remove_copy_hss("+id+")");
		$("#HighSeas tr#H_"+id).removeAttr("style");

		$("#HighSeas tr#Blank input:eq(1)").focus();
	}

	function remove_copy_hss(id) {
		if (id == 1) {
			$("#HighSeas tr#H_1 input").each(function(index) {
				$(this).val("");
			});
			$("#HighSeas tr#H_1").attr("style", "display: none");
		}
		else {
			$("#HighSeas tr#H_"+id).remove();
		}
	}

	$(document).ready(function() {

		changeShipmentType();

		// initialize the Selectize control
		var $shipmentType = $('#ajaxShipmentType').selectize();
		// fetch the instance
		var selectize = $shipmentType[0].selectize;
		
		$('#CargoType').on('change', function() {

			var cargo_type = $('#CargoType').val();
			if (cargo_type == 'Bulk'){
				var splashArray = new Array();
				jQuery.ajax({
					type:"get",
					url:"<?php echo base_url($this->_clspath.$this->_class."/ajaxShipmentType") ?>/" + cargo_type,
					success:function(response){
						var orderList = JSON.parse(response);
						selectize.clearOptions();
						jQuery.each(orderList, function(index, item) {
						   var data = {
				        		'value' :index,
				        		'text' :item,
				        	};
				        	selectize.addOption(data);
				        });
						selectize.addItem('Bulk');
					},
					error:function(error){ 
			  			console.log(error);
					}
				});
			}
			else{
				var splashArray = new Array();
				jQuery.ajax({
					type:"get",
					url:"<?php echo base_url($this->_clspath.$this->_class."/ajaxShipmentType") ?>/" + cargo_type,
					success:function(response){
						var orderList = JSON.parse(response);
						selectize.clearOptions();
						jQuery.each(orderList, function(index, item) {
						   var data = {
				        		'value' :index,
				        		'text' :item,
				        	};
				        	selectize.addOption(data);
				        });
						selectize.addItem('FCL');
					},
					error:function(error){ 
			  			console.log(error);
					}
				});
			}
			$('#ajaxShipmentType').kaabar_autocomplete('<?php echo site_url($this->_clspath.$this->_class."/ajaxShipmentType") ?>/' + cargo_type);
		});

		$('#ajaxShipmentType').on('change', function() {
			changeShipmentType();
		});
		

		function changeShipmentType() {
			var st = $('#ajaxShipmentType').val();
			if (st == 'FCL') {
				$('.Containers').removeAttr('disabled');
			}
			else {
				$('.Containers').val('0');
				$('.Containers').attr('disabled', true);
			}
		}

		if (jQuery.isFunction(jQuery.fn.selectize)) {
			$('#ajaxShipmentType').selectize();
		}

		$('#ajaxParty').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajax') ?>'});
		$('#ajaxShipper').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/Shipper') ?>'});
		$('#ajaxImporter1').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/Importer') ?>'});
		$('#ajaxImporter2').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/Importer') ?>'});
		$('#ajaxOriginAgent').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/OriginAgent') ?>'});
		$('#ajaxforwarder').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/Forwarder') ?>'});
		$('#ajaxLine').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/Line') ?>'});
		$('#ajaxCHA').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/CHA') ?>'});
		$('#ajaxCFS').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/CFS') ?>'});
		$('#ajaxOCountry').kaabar_autocomplete({source: '<?php echo site_url('/master/country/ajax') ?>'});

		$("#ajaxShipmentPort").autocomplete({
		 	source: '<?php echo site_url('master/port/ajax') ?>',
		 	minLength: 1,
		 	focus: function(event, ui) {
		 		$("#ajaxShipmentPort").val( ui.item.name);
		 		return false;
		 	},
		 	select: function(event, ui) {
		 		$("#ajaxShipmentPort").val(ui.item.name);
		 		$("#ShipmentPortID").val(ui.item.id);
		 		$("#OriginCountryID").val(ui.item.country_id);
		 		$("#ajaxOCountry").val(ui.item.country);
		 		return false;
		 	},
		 	response: function(event, ui) {
			if (ui.content.length === 0) {
				$("#ajaxShipmentPort").val('');
				$("#ShipmentPortID").val(0);
				$("#OriginCountryID").val(0);
		 		$("#ajaxOCountry").val('');
			 }
			}
	    })
	    .data('ui-autocomplete')._renderItem = function(ul, item) {
		 	return $('<li></li>')
		 		.data('item.autocomplete', item)
		 		.append("<a>" + item.name + " <span class='tiny'><span class='orange'>" + item.unece_code + "</span> " + item.country + "</span></a>")
		 		.appendTo(ul);
		 };



		$("#ajaxDischargePort").autocomplete({
		 	source: '<?php echo site_url('master/indian_port/ajax') ?>',
		 	minLength: 1,
		 	focus: function(event, ui) {
		 		$("#ajaxDischargePort").val( ui.item.name);
		 		return false;
		 	},
		 	select: function(event, ui) {
		 		$("#ajaxDischargePort").val(ui.item.name);
		 		$("#DischargePortID").val(ui.item.id);
		 		//$("#DestinationPortID").val(ui.item.id);
		 		//$("#ajaxDestinationPort").val(ui.item.name);
		 		return false;
		 	},
		 	response: function(event, ui) {
			if (ui.content.length === 0) {
				$("#ajaxDischargePort").val('');
				$("#DischargePortID").val(0);
				//$("#DestinationPortID").val(0);
		 		//$("#ajaxDestinationPort").val('');
			 }
			}
	    })
		.data('ui-autocomplete')._renderItem = function(ul, item) {
		 	return $('<li></li>')
		 		.data('item.autocomplete', item)
		 		.append("<a>" + item.name + " <span class='tiny'><span class='orange'>" + item.unece_code + "</span> " + item.country + "</span></a>")
		 		.appendTo(ul);
		};

		$("#ajaxDestinationPort").autocomplete({
		 	source: '<?php echo site_url('master/indian_port/ajax') ?>',
		 	minLength: 1,
		 	focus: function(event, ui) {
		 		$("#ajaxDestinationPort").val( ui.item.name);
		 		return false;
		 	},
		 	select: function(event, ui) {
		 		$("#DestinationPortID").val(ui.item.id);
		 		$("#ajaxDestinationPort").val(ui.item.name);
		 		return false;
		 	},
		 	response: function(event, ui) {
			if (ui.content.length === 0) {
				$("#DestinationPortID").val(0);
		 		$("#ajaxDestinationPort").val('');
			 }
			}
	    })
		.data('ui-autocomplete')._renderItem = function(ul, item) {
		 	return $('<li></li>')
		 		.data('item.autocomplete', item)
		 		.append("<a>" + item.name + " <span class='tiny'><span class='orange'>" + item.unece_code + "</span> " + item.country + "</span></a>")
		 		.appendTo(ul);
		};

		$('#hsnCode').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/goods_services/id/sac_hsn') ?>', displayKey: 'sac_hsn'});

	});
</script>

<script type="text/javascript">
	// Close Before check save data or not
	// window.addEventListener("beforeunload", function (e) {
	//     var confirmationMessage = 'It looks like you have been editing something. '
	//                             + 'If you leave before saving, your changes will be lost.';

	//     (e || window.event).returnValue = confirmationMessage; //Gecko + IE
	//     return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
	// });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>


<script type="text/javascript">

	$('#modal-import-document').on('hidden.bs.modal', function () {
        $(this).find('#documentUpload').html('');
    });
    
    $('#modal-containers').on('hidden.bs.modal', function () {
        $(this).find('#updateContainers').html('');
    })

    $('#modal-container').on('hidden.bs.modal', function () {
        $(this).find('#updateContainer').html('');
    })

    $('#modal-costsheet').on('hidden.bs.modal', function () {
        $(this).find('#updateCostsheet').html('');
    })

    function attachDocuments(id = null)
    {   
    	if(! id){
        	return;
        }
        
        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>import/attached_document/index/'+id,
                type: 'post',
                dataType: 'json',
                success:function(response) {
                    
                    var my_html = '';    

					<?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
						my_html += '<form action="<?php echo base_url('import/attached_document/attach/') ?>'+id+'" method="post" id="importDocument">';
							my_html += '<div class="modal-body">';
								my_html += '<div class="table-responsive  table-sm">';
							        my_html += '<table class="table">';
							            my_html += '<thead>';
							                my_html += '<tr>';
							                    my_html += '<th class="text-center align-middle">##</th>';
							                    my_html += '<th class="align-middle">Date</th>';
							                    my_html += '<th width="35%" class="align-middle">Document Name</th>';
							                    my_html += '<th class="align-middle">Remarks</th>';
							                    my_html += '<th class="text-center align-middle">Received</th>';
							                    my_html += '<th width="15%" class="text-center align-middle">Status</th>';
							                    my_html += '<th class="text-center align-middle">Required</th>';
							                    my_html += '<th class="text-center align-middle">Action</th>';
							                my_html += '</tr>';
							            my_html += '</thead>';
							            my_html += '<tbody>';

							            	if(response.documents.length){
							                    var result = JSON.parse(JSON.stringify(response.documents));
							                    var $i;

							                    for(var i=0;i < result.length;i++){
							                        var item = result[i]; 

							                        
							                        if (item.received_date == '00-00-0000')
														var rcvd_date = '-';
													else 
														var rcvd_date = '<span class="badge badge-success p-1">'+item.received_date+'</span>';

							                        if(item.file.length > 0)
							                        	var filename = '<i class="fa fa-paperclip text-success"></i>';
							                        else
							                        	var filename = '<input type="file" name="attach['+item.document_type_id+']" class="filepond">';

							                        var $delete;
							                        if($delete)
							                            var deleteBtn = '<a href="javascript: deleteDocument('+item.id+')" class="btn btn-danger btn-xs"><i class="fa fa-trash-alt"></i></a>';
							                        else
							                            var deleteBtn = '<a href="javascript: deleteDocument('+item.id+')" class="btn btn-danger btn-xs"><i class="fa fa-trash-alt"></i></a>';

							                        my_html += '<tr><td class="text-center align-middle">'+(i+1)+'</td>';
							                        	my_html += '<td class="align-middle">'+item.date+'</td>';
							                            my_html += '<td class="align-middle">';
							                                my_html += '<input type="hidden" name="document_type_id['+item.document_type_id+']" value="'+item.document_type_id+'"><input type="hidden" name="document_id['+item.document_type_id+']" value="'+item.id+'">';
							                            my_html += item.name+'</td>';
							                            my_html += '<td class="align-middle">'+item.remarks+'</td>';
							                            my_html += '<td class="text-center align-middle">'+rcvd_date+'</td>';
							                            my_html += '<td class="text-center align-middle">'+filename+'</td>';
							                            //my_html += '<td class="text-center align-middle"><button type="button" id="#modalUpload" data-dismiss="modal" data-toggle="modal"  class="btn btn-info ml-2" onclick="attachDocument('+<?php echo $row["id"] ?>+','+item.document_type_id+')"> <i class="fa fa-upload"></i> Upload Documents</button></td>';
							                            my_html += '<td class="text-center align-middle">'+item.is_compulsory+'</td>';
							                            my_html += '<td class="text-center align-middle">'+deleteBtn+'</td>';
							                        my_html += '</tr>';
							                    }
							                }
							                else
							                {
							                    my_html += '<tr><td colspan="8" class="align-middle"><div class="alert alert-danger text-center">No Documents Records Found.</div></td></tr>';
							                }

							            my_html += '</tbody>'; 
							        my_html += '</table>';
							    my_html += '</div>';
						    my_html += '</div>';
							my_html += '<div class="modal-footer">';
								my_html += '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
								my_html += '<button type="submit" class="btn btn-success text-left"><i class="fa fa-save"></i> Update</button>';
							my_html += '</div>';
						my_html += '</form>';

					<?php //} ?>        
                   
					if(my_html)
                        $('#documentUpload').append(my_html);
                    
                    $("#modal-import-document").modal('toggle');

                    
                    const inputElements = document.querySelectorAll('input[type="file"]');
			    	Array.from(inputElements).forEach(inputElement => {
			      		FilePond.create(inputElement);
				    });
                    //$("#loader").hide();

                    // submit the edit from 
                    $("#importDocument").unbind('submit').bind('submit', function() {
                        var form = $(this);

                        // // remove the text-danger
                        $(".text-danger").remove();

                        $.ajax({
                            url: form.attr('action') + '/' + id,
                            type: form.attr('method'),
                            data: form.serialize(), // /converting the form data into array and sending it to server
                            dataType: 'json',
                            beforeSend: function(){
                                $("#loader").show();
                            },
                            success:function(response) {
                            	$("#modal-import-document").modal('hide');
                            	<?php
									$alert = $this->session->userdata('alert');
									if($alert) {
										foreach($alert as $class => $message) {
											if (is_array($message)) {
												echo "
									new PNotify({
										title: '" . ucfirst($class) . "',
										text: '<i class=\"icon-angle-double-right\"></i> " . implode('<br /><i class=\"icon-angle-double-right\"></i> ', $message) . "',
										type: '$class',
										nonblock: {
											nonblock: true,
											nonblock_opacity: .2
										}
									});\n";
											}
											else
												echo "
									new PNotify({
										title: '" . ucfirst($class) . "',
										text: '<i class=\"icon-angle-double-right\"></i> " . str_replace("\n", '<br /><i class=\"icon-angle-double-right\"></i> ', $message) . "',
										type: '$class',
										nonblock: {
											nonblock: true,
											nonblock_opacity: .2
										}
									});\n";
										}
										$this->session->unset_userdata('alert');
									}
								?>
                                // if(response.success === true) {
                                //     $("#loader").hide();
                                //     Swal.fire({
                                //         title: 'Wow...!',
                                //         html:  ""+response.messages+"",
                                //         icon: "success",
                                //     }).then(function() {

                                //         if(response.trip === true){
                                //             location.reload(true);
                                //         }
                                //         else{
                                //             $('#modalTracking').html('');
                                //             editGlobalFunc(id);
                                //         }
                                //     });
                                    
                                // }
                                // else
                                // {
                                //     $("#loader").hide();
                                //     if(response.messages instanceof Object) {
                                //         $.each(response.messages, function(index, value) {
                                //             var id = $("#"+index);
                                //             id.removeClass('is-invalid')
                                //             .removeClass('is-valid')
                                //             .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
                                //             id.after(value);
                                //         });
                                //     }
                                //     else
                                //     {
                                //         $("#createGlobalModel").modal('hide');
                                //         Swal.fire({
                                //             title: 'Opps...!!!',
                                //             html:  ""+response.messages+"",
                                //             icon: "error",
                                //         });
                                //     }
                                // }
                            }
                        }); 
                        return false;
                    });

                    $(".deleteLocation").on('click', function() {
                        var itemid = $(this).parents('tr').find('input[type="hidden"]').val();
                        var group_id = id;

                        Swal.fire({
                          title: "Are you sure?",
                          text: "You won't be able to revert this!",
                          icon: "warning",
                          showCancelButton: true,
                          confirmButtonColor: "#3085d6",
                          cancelButtonColor: "#d33",
                          confirmButtonText: "Yes, Delete it...!",
                          cancelButtonText: "Cancel",
                          buttonsStyling: true
                        }).then(function(result) {
                            if (result.value) {
                                $.ajax({
                                    type: "post",
                                    url: "<?php //echo site_url('resource/location/remove/')?>"+itemid+'/'+group_id,
                                    success: function(response){
                                        var answer = JSON.parse(response);
                                        switch ( answer.status ) {
                                            case 'success' :
                                                Swal.fire({
                                                  title: 'Wow...!',
                                                  html:  ""+answer.msg+"",
                                                  icon: "success",
                                                }).then(function() {
                                                    $('#modalTracking').html('');
                                                    editGlobalFunc(group_id);
                                                });
                                            break;
                                            case 'warning' :
                                                Swal.fire({
                                                  title: 'Ohhh...!',
                                                  html:  ""+answer.msg+"",
                                                  icon: "warning",
                                                }); 
                                            break;
                                            case 'error' :
                                                Swal.fire({
                                                  title: 'Opps...!',
                                                  html:  ""+answer.msg+"",
                                                  icon: "error",
                                                }); 
                                            break;
                                        }   
                                    },
                                    error: function (result) {
                                        Swal.fire({
                                          title: "Somathing Wrong...!",
                                          text: "Your Record is not Deleted, Try Again)",
                                          icon: "error",
                                        });
                                    }
                                });
                            } 
                            else if (result.dismiss === Swal.DismissReason.cancel) {
                                Swal.fire({
                                  title: "Cancelled",
                                  text: "Your Records is safe :)",
                                  icon: "error",
                                });
                            }
                        });
                    });


                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
            $("#createGlobalModel").modal('hide');
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

    function updateContainers(id = null)
    {   
    	if(! id){
        	return false;
        }
        
        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>import/container/index/'+id,
                type: 'post',
                dataType: 'json',
                success:function(response) {
                    
                    var my_html = '';    
                    <?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
						my_html += '<form action="<?php echo base_url('import/container/edit/') ?>'+id+'" method="post" id="frmUpdateContainers">';
							my_html += '<div class="modal-body p-0">';
								my_html += '<div class="table-responsive modalcustome">';
							        my_html += '<table class="table table-sm nowrap">';
							            my_html += '<thead>';
							                my_html += '<tr>';
							                    my_html += '<th class="text-center align-middle">##</th>';
							                    my_html += '<th class="align-middle">Container No</th>';
							                    my_html += '<th class="align-middle">Size</th>';
							                    my_html += '<th class="align-middle">Container Type</th>';
							                    my_html += '<th class="align-middle">From (Pickup)</th>';
							                    my_html += '<th class="align-middle">To (Delivery)</th>';
							                    my_html += '<th class="align-middle">Vehicle No</th>';
							                    my_html += '<th class="align-middle">Transporter Name</th>';
							                    my_html += '<th class="align-middle">Line Seal</th>';
							                    my_html += '<th class="align-middle">Shipper Seal</th>';
							                    my_html += '<th class="align-middle">Custom Seal</th>';
							                    my_html += '<th class="align-middle">Rcvd. Nett Weight</th>';
							                my_html += '</tr>';
							            my_html += '</thead>';
							            my_html += '<tbody>';

										if(response.rows.length){
									        var result = JSON.parse(JSON.stringify(response.rows));
									        var $i;

									        for(var i=0;i < result.length;i++){
									            var item = result[i]; 

									            // var editBtn = '<a href="javascript: updateContainer('+id+','+item.id+')" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></a>';
									            // my_html += '<tr><td class="text-center align-middle">'+editBtn+'</td>';
									            // 	my_html += '<td class="align-middle">'+item.number+'</td>';
									            // 	my_html += '<td class="align-middle text-danger"><b>'+item.size+'</b></td>';
									            //     my_html += '<td class="align-middle">'+item.container_type+'</td>';
									            //     my_html += '<td class="align-middle">'+item.from+'</td>';
									            //     my_html += '<td class="align-middle">'+item.to+'</td>';
									            //     my_html += '<td class="align-middle">'+item.vehicle_no+'</td>';
									            //     my_html += '<td class="align-middle">'+item.transporter+'</td>';
									            //     my_html += '<td class="align-middle">'+item.line_seal+'</td>';
									            //     my_html += '<td class="align-middle">'+item.shipper_seal+'</td>';
									            //     my_html += '<td class="align-middle">'+item.custom_seal+'</td>';
									            //     my_html += '<td class="align-middle text-right">'+item.net_weight+'</td>';
									            // my_html += '</tr>';

												my_html += '<tr>';
													my_html += '<td class="align-middle text-center"><b>'+(i+1)+'</b></td>';
													my_html += '<td class="text-center align-middle">';
														my_html += '<input type="hidden" name="id['+item.id+']" value="'+item.id+'">';
														my_html += '<input type="text" class="form-control form-control-sm" name="number['+item.id+']" id="number['+item.id+']" value="'+item.number+'">';
													my_html += '</td>';
													my_html += '<td class="align-middle text-danger text-center"><b>'+item.size+'</b></td>';
												    my_html += '<td class="align-middle">'+item.container_type+'</td>';
												    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="from['+item.id+']" id="from['+item.id+']" value="'+item.from+'"></td>';
												    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="to['+item.id+']" id="to['+item.id+']" value="'+item.to+'"></td>';
												    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="vehicle_no['+item.id+']" id="vehicle_no['+item.id+']" value="'+item.vehicle_no+'"></td>';
												    my_html += '<td class="align-middle">'+item.transporter+'</td>';
												    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="line_seal['+item.id+']" id="line_seal['+item.id+']" value="'+item.line_seal+'" onkeypress="return isNumber(event)"></td>';
												    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="shipper_seal['+item.id+']" id="shipper_seal['+item.id+']" value="'+item.shipper_seal+'" onkeypress="return isNumber(event)"></td>';
												    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" name="custom_seal['+item.id+']" id="custom_seal['+item.id+']" value="'+item.custom_seal+'" onkeypress="return isNumber(event)"></td>';
												    my_html += '<td class="align-middle"><input type="text" class="form-control form-control-sm" onkeypress="return isNumberDot(event)" name="net_weight['+item.id+']" id="net_weight['+item.id+']" value="'+item.net_weight+'"></td>';
												my_html += '</tr>';

									        }
									    }
							                else
							                {
							                    my_html += '<tr><td colspan="12" class="align-middle"><div class="alert alert-danger text-center">No Documents Records Found.</div></td></tr>';
							                }

							            my_html += '</tbody>'; 
							        my_html += '</table>';
							    my_html += '</div>';
						    my_html += '</div>';
							my_html += '<div class="modal-footer">';
								my_html += '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
								my_html += '<button type="submit" class="btn btn-success text-left"><i class="fa fa-save"></i> Update</button>';
							my_html += '</div>';
						my_html += '</form>';

					<?php //} ?>        
                   
					if(my_html)
                        $('#updateContainers').append(my_html);
                    
                    $("#modal-containers").modal('show');

                    /////////////////////////////////////////////////////////////////////////////////////

                    $('.select2').select2({
				      theme: 'bootstrap4'
				    })

				    $('.select2-selection__rendered').hover(function () {
					    $(this).removeAttr('title');
					});

					// on first focus (bubbles up to document), open the menu
					$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
					  $(this).closest(".select2-container").siblings('select:enabled').select2('open');
					});

					// steal focus during close - only capture once and stop propogation
					$('select.select2').on('select2:closing', function (e) {
					  $(e.target).data("select2").$selection.one('focus focusin', function (e) {
					    e.stopPropagation();
					  });
					});

					$(document).on('select2:open', () => {
					    document.querySelector('.select2-search__field').focus();
					});

					/////////////////////////////////////////////////////////////////////////////////////

                    $("#frmUpdateContainers").unbind('submit').on('submit', function() {
						var form = $(this);

						// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: form.attr('action'),
						  	type: form.attr('method'),
						  	data: form.serialize(), // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	success:function(response) {
						  		if(response.success === true) {
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    }).then(function() {
								    	$("#frmUpdateContainers")[0].reset();
								  		$("#frmUpdateContainers .form-control").removeClass('is-invalid').removeClass('is-valid');
								  		//$("#modal-containers").modal('hide');
								  		$('#updateContainers').html('');
										updateContainers(id);
									    //$("#modal-containers").modal('show');
								    });
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								  } 
								  else 
								  {
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
							}
						}); 
						return false;
					});

                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
            $("#modal-containers").modal('hide');
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }

    function updateContainer(job_id = null, id = null)
    {   

    	if(job_id & id){

    		$.ajax({
                url: '<?php echo site_url() ?>import/container/edit/'+job_id+'/'+id,
                type: 'get',
                dataType: 'json',

                success:function(response) {

                	if(response.success == true){

                		var my_html = '';    
                		<?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
							my_html += '<form action="<?php echo base_url('import/container/edit/') ?>'+job_id+'/'+id+'" method="post" id="frmUpdateContainer">';
								my_html += '<div class="modal-body">';
									my_html += '<div class="row">';
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Container No</label>';
												my_html += '<input type="hidden" name="id" value="'+id+'">';
												my_html += '<input type="hidden" name="job_id" value="'+job_id+'">';
												my_html += '<input type="text" class="form-control form-control-sm" name="number" id="number" value="'+response.row.number+'">';
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Type</label>';
												my_html += response.container_types;
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">From (Pickup Location)</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="from" id="from" value="'+response.row.from+'">';
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">To</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="to" id="to" value="'+response.row.to+'">';
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Vehicle No</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="vehicle_no" id="vehicle_no" value="'+response.row.vehicle_no+'">';
											my_html += '</div>';
										my_html += '</div>';
									
										my_html += '<div class="col-md-6">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Transporter</label>';
												my_html += response.transporter;
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-4">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Line Seal</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="line_seal" id="line_seal" value="'+response.row.line_seal+'">';
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-4">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Shipper Seal</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="shipper_seal" id="shipper_seal" value="'+response.row.shipper_seal+'">';
											my_html += '</div>';
										my_html += '</div>';

										my_html += '<div class="col-md-4">';
											my_html += '<div class="form-group">';
												my_html += '<label class="control-label">Custome Seal</label>';
												my_html += '<input type="text" class="form-control form-control-sm" name="custom_seal" id="custom_seal" value="'+response.row.custom_seal+'">';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';

								my_html += '<div class="modal-footer">';
									my_html += '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
									my_html += '<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>';
								my_html += '</div>';
							my_html += '</form>';

						<?php //} ?>   


                	}
                	else
	                {
	                	Swal.fire({
                            title: 'Opps...!!!',
                            html:  ""+response.messages+"",
                            icon: "error",
                        });
	                }
                	
	                if(my_html)
                        $('#updateContainer').append(my_html);
                    
                    $("#modal-containers").modal('hide');
                    $("#modal-container").modal('toggle');

					$("#frmUpdateContainer").unbind('submit').on('submit', function() {
						var form = $(this);

						// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: form.attr('action'),
						  	type: form.attr('method'),
						  	data: form.serialize(), // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	success:function(response) {
						  		if(response.success === true) {

									$("#modal-container").modal('hide');
									updateContainers(job_id);
								    //$("#modal-containers").modal('show');

									Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    }).then(function() {
								    	$("#frmUpdateContainer")[0].reset();
								  		$("#frmUpdateContainer .form-control").removeClass('is-invalid').removeClass('is-valid');
								    });
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								  } 
								  else 
								  {
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
							}
						}); 
						return false;
					});



                },
                error: function (result) {
                    $("#loader").hide();
                    Swal.fire({
				        title: '(: Opps :)',
				        html:  "Something went wrong...!",
				        icon: "error",
				    }).then(function() {
				    	return false
				    });
                },
            });
        }
        else
        {
            Swal.fire({
		        title: '(: Opps :)',
		        html:  "Something went wrong...!",
		        icon: "error",
		    }).then(function() {
		    	return false
		    });
        }
    }

    function updateCostsheet(id = null)
    {   
    	if(! id){
        	return false;
        }
        
        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>import/costsheet/index/'+id,
                type: 'post',
                dataType: 'json',
                success:function(response) {

                	if(response.rows.length){
				    	var result = JSON.parse(JSON.stringify(response.rows));
				    	var total_purchase = 0;
						var total_sell = 0;
						for (var i = 0; i < result.length; i++) {
					    	total_purchase += result[i].amount << 0;
					    	total_sell += result[i].sell_amount << 0;
						}
					}

                	var sr_no = 0;
                    var my_html = '';    
                    <?php //if(Auth::hasPermission(Auth::READ, ['resource/location'])){ ?>
						my_html += '<form action="<?php echo base_url('import/costsheet/edit/') ?>'+id+'" method="post" id="frmUpdateCostsheet" enctype="multipart/form-data">';
							my_html += '<div class="modal-body p-0">';
								my_html += '<div class="table-responsive modalcustome">';
							        my_html += '<table class="table table-sm table-bordered DataEntry">';
							            my_html += '<thead>';
							            	my_html += '<tr>';
							                    my_html += '<th class="text-center align-middle width-50" rowspan="2"></th>';
							                    my_html += '<th class="text-center align-middle width-50" rowspan="2">##</th>';
							                    my_html += '<th class="align-middle width-200" rowspan="2">Charge Head</th>';
							                    my_html += '<th class="align-middle width-200" rowspan="2">Vendor Name</th>';
							                    my_html += '<th class="text-center align-middle" colspan="7">Buy (Inward)</th>';
							                    my_html += '<th class="text-center align-middle" colspan="7">Sell (Outward)</th>';
							                    my_html += '<th class="text-center align-middle width-200" rowspan="2">Doc Upload</th>';
							                    my_html += '<th class="text-center align-middle width-80" rowspan="2"><a href="#" class="CheckAll"><i class="fa fa-trash-alt"></i></a></th>';
							                my_html += '</tr>';
							                my_html += '<tr>';
							                    my_html += '<th class="align-middle text-center width-80">Currancy</th>';
							                    my_html += '<th class="align-middle text-center width-80">EX Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">INR</th>';
							                    my_html += '<th class="align-middle text-center width-80">Unit</th>';
							                    my_html += '<th class="align-middle text-center width-80">Qty</th>';
							                    my_html += '<th class="align-middle text-center width-80">Amt</th>';
							                    
							                    my_html += '<th class="align-middle text-center width-80">Currancy</th>';
							                    my_html += '<th class="align-middle text-center width-80">EX Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">Rate</th>';
							                    my_html += '<th class="align-middle text-center width-80">INR</th>';
							                    my_html += '<th class="align-middle text-center width-80">Unit</th>';
							                    my_html += '<th class="align-middle text-center width-80">Qty</th>';
							                    my_html += '<th class="align-middle text-center width-80">Amt</th>';
							                    
							                my_html += '</tr>';
							            my_html += '</thead>';
										my_html += '<tbody>';
										
										var dnone = "d-none";
										if(response.rows.length){
										    var result = JSON.parse(JSON.stringify(response.rows));
										    var $i;

										    if(result){

											    for(var i=0;i < result.length;i++){
											        var item = result[i]; 
											        
											        if(item.id > 0) {

											        	var $non_stax = '';
											        	if(item.stax_code)
															if (item.stax_code.length == 0)
																$non_stax = 'NonSTax';

														//var total = round(item.amount, 2);
														
														my_html += '<tr>';
														my_html += '<td class="align-middle text-center grayLight '+(item.category === 'Bill Items' ? 'SortHandle' : 'ui-state-disabled')+'"><i class="icon-bars"></i></td>';
														if (item.category == 'Bill Items') 
															my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Validate text-center" name="kbr_sr_no['+item.id+']" value="'+item.sr_no+'" required></td>';
														else
															my_html += '<td class="align-middle '+$non_stax+'"><input type="hidden" class="form-control form-control-sm Numeric Validate text-center" name="kbr_sr_no['+item.id+']" value="0"></td>';

														my_html += '<td class="'+$non_stax+'"><input type="hidden" class="form-control form-control-sm BillItemID" name="kbr_bill_item_id['+item.id+']" value="'+item.bill_item_id+'">';
														my_html += '<input type="text" class="form-control form-control-sm BillItemCode" value="'+item.particulars+'" name="kbr_bill_item_code['+item.id+']" required></td>';
														my_html += '<td class="align-middle '+$non_stax+'">';

														my_html += '<input type="text" class="form-control form-control-sm VendorName" value="'+item.vendor_name+'" required>';
														my_html += '<input type="hidden" class="form-control form-control-sm" name="kbr_vendor_id['+item.id+']" value="'+item.vendor_id+'" required></td>'
														
														////// Start Purchase Section
														my_html += '<td class="align-middle '+$non_stax+'">';
															my_html += item.is_inr;
															my_html += '<input type="text" class="Autocomplete form-control form-control-sm CurrencyName Unchanged Validate" value="'+item.currency_name+'" name="kbr_currency_name['+item.id+']" required>';
															my_html += '<input type="hidden" class="Unchanged CurrencyNameId" value="'+item.currency_id+'" name="kbr_currency['+item.id+']">'
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'">';
															my_html += '<input type="text" class="form-control form-control-sm Unchanged ExchangeRate Validate text-right" value="'+item.ex_rate+'" name="kbr_ex_rate['+item.id+']" required>';
															my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric Currency text-right" name="kbr_currency_amount['+item.id+']" value="'+item.currency_amt+'" onkeypress="return isNumberDot(event)" >';
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Rate" name="kbr_rate['+item.id+']" 	value="'+item.rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm InrRate Numeric" name="kbr_inr_rate['+item.id+']" readonly value="'+item.inr_rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'">'+item.unit_id+'</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Units Validate" name="kbr_units['+item.id+']" value="'+item.qty+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric Amount Validate" name="kbr_amount['+item.id+']" readonly value="'+item.amount+'" required></td>';
														// ////// END Purchase Section

														// ////// Start Sell Section
														my_html += '<td class="align-middle '+$non_stax+'">';

															my_html += item.sell_is_inr;
															my_html += '<input type="text" class="form-control form-control-sm SellCurrencyName Unchanged Validate" value="'+item.sell_currency_name+'" name="kbr_sell_currency_name['+item.id+']" required>';
															my_html += '<input type="hidden" class="SellCurrencyNameId Unchanged" value="'+item.sell_currency_id+'" name="kbr_sell_currency['+item.id+']">'
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'">';
															my_html += '<input type="text" class="form-control form-control-sm SellExchangeRate Unchanged Validate text-right" value="'+item.sell_ex_rate+'" name="kbr_sell_ex_rate['+item.id+']" required>';
															my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric SellCurrency text-right" name="kbr_sell_currency_amount['+item.id+']" value="'+item.sell_currency_amt+'" onkeypress="return isNumberDot(event)">';
														my_html += '</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm  Numeric SellRate" name="kbr_sell_rate['+item.id+']" value="'+item.sell_rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm SellInrRate Numeric" name="kbr_sell_inr_rate['+item.id+']" readonly value="'+item.sell_inr_rate+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'">'+item.sell_unit_id+'</td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric SellUnits Validate" name="kbr_sell_units['+item.id+']" value="'+item.sell_qty+'" required></td>';

														my_html += '<td class="align-middle '+$non_stax+'"><input type="text" class="form-control form-control-sm Numeric SellAmount Validate" name="kbr_sell_amount['+item.id+']" readonly value="'+item.sell_amount+'" required></td>';
														////// End Sell Section
														if(item.upfile != null){
															//my_html += '<td class="align-middle '+$non_stax+'"><input type="file" class="Fileupload d-none" name="kbr_upload['+item.id+']"></td>';
															my_html += '<td class="align-middle text-center '+$non_stax+'">';
															my_html += '<input type="hidden" class="ItemRowID" name="kbr_row_id['+item.id+']" value="'+item.id+'">';

															my_html += '<a href="'+item.upfile+'" download><i class="fa fa-download fa-lg text-success"></i></a>';
															my_html += '<a href="" class="deleteAttach"><i class="fa fa-trash-alt fa-lg text-danger ml-2"></i></a></td>';
															my_html += '</td>';
														}
														else
														{



															my_html += '<td class="align-middle '+$non_stax+'"><input type="file" class="Fileupload" name="kbr_upload['+item.id+']"></td>';
														}

														my_html += "<td class='align-middle text-center'"+$non_stax+"'>";
															my_html +=  item.delete_btn;
														my_html += "</td>";

														my_html += '</tr>';

														sr_no = item.sr_no;
													}
												}

											}
										}
										else
										{
											var dnone = "";
										}

										sr_no++
										my_html += '<tr class="TemplateRow '+dnone+'">';
											my_html += '<td class="align-middle text-center SortHandle"><i class="icon-bars"></i></td>';
											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment text-center" name="new_sr_no[]" value="'+(sr_no)+'"></td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm BillItemCode Focus" value="" name="new_bill_item_code[]" >';
												my_html += '<input type="hidden" class="form-control form-control-sm BillItemID" name="new_bill_item_id[]" value="">';
											my_html += '</td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm VendorName Autocomplete" value="" >';
												my_html += '<input type="hidden" class="form-control form-control-sm" name="new_vendor_id[]" value="" >'
											my_html += '</td>';
											////// Start Purchase Section
											my_html += '<td>';
												my_html += '<?php echo form_dropdown_single('new_is_inr[]', array('Yes'=>'Yes', 'No'=>'No'), 'Yes', 'class="form-control form-control-sm Unchanged IsINR d-none"') ?>';

												my_html += '<input type="text" class="form-control form-control-sm CurrencyName Unchanged Validate" value="INR - Indian Rupees" name="new_currency_name[]" >';
												my_html += '<input type="hidden" class="Unchanged CurrencyNameId" value="1" name="new_currency[]">'
											my_html += '</td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm Unchanged ExchangeRate Validate text-right" value="1" name="new_ex_rate[]" >';
												my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric Currency text-right" name="new_currency_amount[]" value="1" onkeypress="return isNumberDot(event)" >';
											my_html += '</td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Rate" name="new_rate[]" 	value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm InrRate Numeric" name="new_inr_rate[]" readonly value="" ></td>';

											my_html += '<td><?php echo form_dropdown_single('new_unit[]', getSelectOptions('units'), '', 'class="Unchanged form-control form-control-sm Unit"') ?></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Units Validate" name="new_units[]" value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric Amount Validate" name="new_amount[]" readonly value="" ></td>';
											////// END Purchase Section

											////// Start Sell Section
												my_html += '<td>';
												my_html += '<?php echo form_dropdown_single('new_sell_is_inr[]', array('Yes'=>'Yes', 'No'=>'No'), 'Yes', 'class="form-control form-control-sm Unchanged SellIsINR d-none"') ?>';

												my_html += '<input type="text" class="form-control form-control-sm SellCurrencyName Unchanged Validate" value="INR - Indian Rupees" name="new_sell_currency_name[]" >';
												my_html += '<input type="hidden" class="SellCurrencyNameId Unchanged" value="1" name="new_sell_currency[]">'
											my_html += '</td>';

											my_html += '<td>';
												my_html += '<input type="text" class="form-control form-control-sm SellExchangeRate Unchanged Validate text-right" value="1" name="new_sell_ex_rate[]" >';
												my_html += '<input type="hidden" class="form-control form-control-sm Unchanged Numeric SellCurrency text-right" name="new_sell_currency_amount[]" value="1" onkeypress="return isNumberDot(event)">';
											my_html += '</td>';

											my_html += '<td><input type="text" class="form-control form-control-sm  Numeric SellRate" name="new_sell_rate[]" value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm SellInrRate Numeric" name="new_sell_inr_rate[]" readonly value="" ></td>';

											my_html += '<td><?php echo form_dropdown_single('new_sell_unit[]', getSelectOptions('units'), '', 'class="form-control form-control-sm SellUnit Unchanged"') ?></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric SellUnits Validate" name="new_sell_units[]" value="" ></td>';

											my_html += '<td><input type="text" class="form-control form-control-sm Numeric SellAmount Validate" name="new_sell_amount[]" readonly value="" ></td>';
											////// End Sell Section
											my_html += '<td><input type="file" class="" name="new_upload[]"></td>';
											my_html += '<td class="align-middle text-center"><button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button></td>';
											//my_html += '<td class="align-middle text-center"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>';
											my_html += '</tr>';


										my_html += '</tbody>'; 
							        my_html += '</table>';
							    my_html += '</div>';


							function numberWithCommas(x) {
							    return x.toString().split('.')[0].length > 3 ? x.toString().substring(0,x.toString().split('.')[0].length-3).replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + x.toString().substring(x.toString().split('.')[0].length-3): x.toString();
							}

							my_html += '</div>';
						    my_html += '<div class="modal-body">';
						    	my_html += '<div class="row">';
						    		my_html += '<div class="col-md-6">';
						    			my_html += '<div class="row">';
						    				my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-danger"><b>Total Purchase ( Inword) :</b></h5>';
											my_html += '</div>';
											my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-danger"><b><span id="TotalPurchase">';
													if(total_purchase > 0)
														my_html += numberWithCommas(total_purchase.toFixed(2));	
													else
														my_html += '0.00';	

												my_html += '</span></b></h5>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
									my_html += '<div class="col-md-6">';
						    			my_html += '<div class="row">';
						    				my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-success"><b>Total Sell (Ourward) :</b></h5>';
											my_html += '</div>';
											my_html += '<div class="col-md-6">';
												my_html += '<h5 class="text-success"><b><span id="TotalSell">';
													if(total_sell > 0)
														my_html += numberWithCommas(total_sell.toFixed(2));	
													else
														my_html += '0.00';	

												my_html += '</span></b></h5>';
											my_html += '</div>';
										my_html += '</div>';
									my_html += '</div>';
								my_html += '</div>';
							my_html += '</div>';
							my_html += '<div class="modal-footer">';
								my_html += '<button type="submit" class="btn btn-success AddButton mr-auto"><i class="icon-white icon-plus"></i> New Item</button>';
								my_html += '<button type="button" onClick="closeCostsheetModel()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
								my_html += '<button type="submit" class="btn btn-success text-left"><i class="fa fa-save"></i> Update</button>';
							my_html += '</div>';
						my_html += '</form>';

					<?php //} ?>        
                   
					if(my_html)
                        $('#updateCostsheet').append(my_html);
                    
                    $("#modal-costsheet").modal('show');

                    ////// CLONE FUNCTION
					function cloneRow(template_row) {

						$new_row = $(template_row).clone();
						$new_row.children('td:last').html(del_button);
						$new_row.removeClass('TemplateRow');
						$new_row.removeClass('d-none');
						$new_row.addClass('lastRowElement');
						$new_row.insertBefore($(template_row));

						$(template_row).find('textarea').each(function(index, el) {
							$new_row.find('textarea').eq(index).val($(this).val());
						});
						
						$(template_row).find('select').each(function(index, el) {
							$new_row.find('select').eq(index).val($(this).val());
						});
						$(template_row).find(':input').not('.Unchanged').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', '');
							else
								$(this).val('');
							
						});

						$(template_row).find('input[type="checkbox"]').not('.Unchanged').prop('checked', '');

						$(template_row).find('td.ClearText').each(function() {
							$(this).text('');
						});
						$(template_row).find('.Increment').each(function() {
							if ($(this).hasClass('tt-input'))
								$(this).typeahead('val', (parseInt($(this).val(), 10)+1));
							else
								$(this).val(parseInt($(this).val(), 10)+1);
						});
						$(template_row).find('.DataDefault').each(function() {
							$(this).val($(this).attr('data-default'));
						});

						$(template_row).find(':input.Focus').focus();
					}

					////// CLONE NEW ROW
					$('#modal-costsheet').unbind().on('click', 'button.AddButton', function(e) {

						var submit_row = $(this).hasClass('RowSubmit');
						var clone_row  = true;
						$template_row  = $(this).parents('#updateCostsheet').find('table.DataEntry').find('tr.TemplateRow');
						$validate_row  = $(this).parents('#updateCostsheet').find('table.DataEntry').find('tr.lastRowElement');

						$validate_row.find('.Validate').each(function(index, el) {
							if ($(this).val() === '') {
								//console.log($(this).attr('class'));
								clone_row = false;
								$(this).addClass('is-invalid');
							}
							else {
								$(this).removeClass('is-invalid');
							}
						});

						if (clone_row) {
							
							cloneRow($template_row);
							
							$('.BillItemCode').cskaabar_autocomplete({source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>'});
							$('.VendorName').kaabar_autocomplete({source: '<?php echo site_url('accounting/ledger/ajax') ?>'});
							$('.CurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.IsINR'});
							$('.SellCurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.SellIsINR'});

							
							
							if (submit_row === false) {
								e.preventDefault();
							}
						}
						else {
							e.preventDefault();
						}
					});

					// Data Entry +/-
					var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';

					////// Delete Attachment
					$('table.DataEntry').on('click', 'a.deleteAttach', function(event) {
						var job_id = id;
						var itemrow_id = $(this).parents('tr').find('.ItemRowID').val();
						Swal.fire({
		                	title: "Are you sure?",
							text: "You want to delete this attachement..!",
							icon: "warning",
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {
		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		event.preventDefault();
					       		event.stopPropagation();
		                	}
		                	else if (result.value) {
		                		$.ajax({
								  	url: '<?php echo site_url('import') ?>/costsheet/deleteattach',
								  	type: 'POST',
								  	data: { job_id : job_id, row_id : itemrow_id },
								  	dataType: 'json',
								  	success:function(response) {
								  		if(response.success === true) {
								  			Swal.fire({
										        title: 'Wow...!',
										        html:  ""+response.messages+"",
										        icon: "success",
										    }).then(function(result) {
										    	$('#modal-costsheet').find('#updateCostsheet').html('');
										    	updateCostsheet(job_id);
										    });
										} 
										else 
										{
										  	Swal.fire({
										        title: 'Opps...!',
										        html:  ""+response.messages+"",
										        icon: "error",
										    });
										}
										return false;
									}
								}); 
		                	}
		                });
		                event.preventDefault();
					    event.stopPropagation();

					});

					////// New Added Row Remove Confirmation
					$('table.DataEntry').on('click', 'button.DelButton', function() {
						const row = $(this).parents('tr');
						const that = this // here a is change
			            var output = true;
		                Swal.fire({
		                	title: "Are you sure?",
							text: "You won't be able to revert this!",
							icon: "warning",
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {

		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		output = false;
		                        return false;
		                	}
		                	else if (result.value) {
								row.remove();		
		                	}
		                });
			        });

					////// Focus On Select All 
				    $("input[type='text']").click(function () {
					   $(this).select();
					});

				    ////// Existing Row Remove Confirmation
				    $('.DataEntry').on('change', '.DeleteCheckbox', function() {
						const row = $(this).closest('tr');
						const that = this // here a is change
			            if ($(this).is(":checked")){
			                var output = true;
			                 Swal.fire({
			                	title: "Are you sure?",
								text: "You won't be able to revert this!",
								icon: "warning",
								showCancelButton: true,
								confirmButtonColor: "#3085d6",
								cancelButtonColor: "#d33",
								confirmButtonText: "Yes, Delete it...!",
								cancelButtonText: "Cancel",
								buttonsStyling: true
			                }).then(function(result) {

			                	if (result.dismiss === Swal.DismissReason.cancel) {
			                		that.checked= false;// here is a change
			                        output = false;
			                	}
			                	else if (result.value) {
									if (!that.checked)
							            row.fadeIn('slow');
							        else 
							            row.fadeOut('slow');	
			                	}
			                });
			            }
			        });

				    ///////// Autocomplete Bill Item
     				$('.BillItemCode').cskaabar_autocomplete({source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>'});
     				///////// Autocomplete Vendor Name
					$('.VendorName').kaabar_autocomplete({source: '<?php echo site_url('accounting/ledger/ajax') ?>'});
					

					/////// PURCHASE SECTION
					///////// Autocomplete Purchase Currancy Name
					$('.CurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.IsINR'});

					$('.DataEntry').on('keyup', '.ExchangeRate', function() {

						var ex_rate  = $(this).val();
						var unit     = $(this).parents('tr').children('td').find('.Units').val();
						var is_inr   = $(this).parents('tr').children('td').find('.IsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.Rate').val();
						var currency = $(this).parents('tr').children('td').find('.Currency');
						var inr_rate = $(this).parents('tr').children('td').find('.InrRate');
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						
						$(this).parents('tr').children('td').find('.Amount').val(amount);
						getTotal();
					});

					$('.DataEntry').on('keyup', '.Units', function() {
						var ex_rate  = $(this).parents('tr').children('td').find('.ExchangeRate').val();
						var unit     = $(this).val();
						var is_inr   = $(this).parents('tr').children('td').find('.IsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.Rate').val();
						var currency = $(this).parents('tr').children('td').find('.Currency');
						var inr_rate = $(this).parents('tr').children('td').find('.InrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.Amount').val(amount);
						$(this).parents('tr').children('td').find('.SellUnits').val(unit);
						getTotal();
					});

					$('.DataEntry').on('keyup', '.Rate', function() {
						var ex_rate = $(this).parents('tr').children('td').find('.ExchangeRate').val();
						var rate    = $(this).val();
						var is_inr  = $(this).parents('tr').children('td').find('.IsINR').val();
						var unit    = $(this).parents('tr').children('td').find('.Units').val();
						var currency = $(this).parents('tr').children('td').find('.Currency');
						var inr_rate = $(this).parents('tr').children('td').find('.InrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.Amount').val(amount);
						getTotal();
					});
					function getTotal(){
						var total_amount = 0;
					    $(".Amount").each(function(){
					        total_amount += +$(this).val();
					    });

					    function numberWithCommas(x) {
						    return x.toString().split('.')[0].length > 3 ? x.toString().substring(0,x.toString().split('.')[0].length-3).replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + x.toString().substring(x.toString().split('.')[0].length-3): x.toString();
						}

					    $("#TotalPurchase").html(numberWithCommas(total_amount.toFixed(2)));	
					}
					///////// END PURCHASE SECTION						

					/////// SALE SECTION
					///////// Autocomplete Sale Currancy Name
					$('.SellCurrencyName').kaabar_autocomplete({source: '<?php echo site_url('master/currency/ajax/currencies') ?>', otherValue: '.SellIsINR'});

					$('.DataEntry').on('keyup', '.SellExchangeRate', function() {

						var ex_rate  = $(this).val();
						var unit     = $(this).parents('tr').children('td').find('.SellUnits').val();
						var is_inr   = $(this).parents('tr').children('td').find('.SellIsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.SellRate').val();
						var currency = $(this).parents('tr').children('td').find('.SellCurrency');
						var inr_rate = $(this).parents('tr').children('td').find('.SellInrRate');
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.SellAmount').val(amount);
						getSellTotal();
					});

					$('.DataEntry').on('keyup', '.SellUnits', function() {
						var ex_rate  = $(this).parents('tr').children('td').find('.SellExchangeRate').val();
						var unit     = $(this).val();
						var is_inr   = $(this).parents('tr').children('td').find('.SellIsINR').val();
						var rate     = $(this).parents('tr').children('td').find('.SellRate').val();
						var currency = $(this).parents('tr').children('td').find('.SellCurrency');
						var inr_rate = $(this).parents('tr').children('td').find('.SellInrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.SellAmount').val(amount);
						getSellTotal();
					});

					$('.DataEntry').on('keyup', '.SellRate', function() {
						var ex_rate = $(this).parents('tr').children('td').find('.SellExchangeRate').val();
						var rate    = $(this).val();
						var is_inr  = $(this).parents('tr').children('td').find('.SellIsINR').val();
						var unit    = $(this).parents('tr').children('td').find('.SellUnits').val();
						var currency = $(this).parents('tr').children('td').find('.SellCurrency');
						var inr_rate = $(this).parents('tr').children('td').find('.SellInrRate');	
						if (is_inr   == 'No') {
							var c_amount = roundOff((unit * rate), 0);
							var amount   = roundOff((unit * rate * ex_rate), 0);
							var inr_amount = roundOff((rate * ex_rate), 0);
							$(currency).val(c_amount);
							$(inr_rate).val(inr_amount);
						}
						else {
							$(currency).val('0');
							var inr_amount = roundOff((rate), 0);
							$(inr_rate).val(inr_amount);
							var amount = roundOff((unit * rate), 0);
						}
						$(this).parents('tr').children('td').find('.SellAmount').val(amount);
						getSellTotal();
					});

					function getSellTotal(){
						var total_amount = 0;
					    $(".SellAmount").each(function(){
					        total_amount += +$(this).val();
					    });

					    function numberWithCommas(x) {
						    return x.toString().split('.')[0].length > 3 ? x.toString().substring(0,x.toString().split('.')[0].length-3).replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + x.toString().substring(x.toString().split('.')[0].length-3): x.toString();
						}

					    $("#TotalSell").html(numberWithCommas(total_amount.toFixed(2)));	
					}
					/////// END SALE SECTION
					
					/////// Submit Form Data
                    $("#frmUpdateCostsheet").unbind('submit').on('submit', function() {

                    	//$(this).validate();
                    	var formData = new FormData(this);

                    	//var form = $(this);
                    	
                    	// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: $(this).attr('action'),
						  	type: $(this).attr('method'),
						  	data: formData, // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	contentType: false,
    						processData: false,
						  	success:function(response) {
						  		closeCostsheetModel();
						  		if(response.success === true) {
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    });
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								  } 
								  else 
								  {
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
								return false;
							}
						}); 
						return false;
					});
                },
                error: function (result) {
                    $("#loader").hide();
                },
            });
        }
        else
        {
            $("#modal-containers").modal('hide');
            Swal.fire({
              title: 'Opps...!',
              html:  'Somathing Wrong Please Try Again',
              icon: "error",
            }); 
        }
    }


    function closeCostsheetModel(){
    	$('#modal-costsheet').modal('hide');
    	$('#modal-costsheet').find('#updateCostsheet').html('');
    }

</script>

