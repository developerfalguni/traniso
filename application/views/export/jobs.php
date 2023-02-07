<div class="card">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>') ?></span><?php strtoupper($page_title) ?></h3>
		<div class="card-tools">
			<?php echo form_dropdown('branch_id', ['' => 'Select Job']+getSelectOptions('branches', 'id', 'name', 'where company_id = '.$company_id), $row['branch_id'], 'class="SelectizeKaabar"'); ?>
			<ol class="breadcrumb float-sm-right m-0">
	  			<li class="breadcrumb-item"><a href="#"><?php echo anchor('main','Dashboard') ?></a></li>
	  			<li class="breadcrumb-item active mr-1"><?php echo humanize(clean($this->_clspath).' / '.$this->_class) ?></li>
			</ol>
		</div>
	</div>
	<div class="card-body p-0">
		<div class="row p-3 mb-2">
			<?php 
				$buffer = null;

				if((isset($show_search) ? $show_search : FALSE)) {
					$buffer .= '<div class="col-xs-8 col-sm-3 col-md-3">' .
						form_open($this->uri->uri_string(), 'class="form-search"') .
						form_hidden(array('search_form' => 1)) . 
						'<div class="input-group input-group-sm">
							<input type="text" class="form-control form-control-sm Focus" name="search" value="' . (isset($search) ? $search : '') . '" id="Search">
							<span class="input-group-append">
								<button class="btn btn-primary" type="submit" id="SearchButton"><i class="fa fa-search"></i><span class="hidden-xs"> Search</span></button>
							</span>
						</div>
						</form>
					</div>';
				}
				else if(isset($list['search_form'])) {
					$buffer .= '<div class="col-xs-8 col-sm-3 col-md-3">' . $this->load->view($list['search_form'], null, true) . '</div>';
				}

				if (isset($buttons)) {
					$buffer .= '<div class="col-xs-4 col-sm-3 col-md-4">';
					foreach ($buttons as $btn)
						$buffer .= $btn . '&nbsp;';
					$buffer .= '</div>';
				}

				if (isset($this->pagination)) {
					$page_links = $this->pagination->create_select_links();
					if ($page_links != FALSE) {
						$buffer .= '<div class="col-xs-12 col-sm-6 col-md-5">';

						$buffer .= '<div class="btn-group btn-group-sm float-right">';

						if (isset($page_links['first_page'])) {
							$buffer .= '<a href="' . $page_links['base_url'].$page_links['first_page'] . '" class="btn btn-info" id="FirstPage" rel="tooltip" data-original-title="Alt + F"><i class="fa fa-step-backward"></i> First</a> ';
						}
						else {
							$buffer .= '<a href="#" class="btn btn btn-info disabled"><i class="fa fa-step-backward"></i> First</a> ';
						}

						if (isset($page_links['previous_page'])) {
							$buffer .= '<a href="' . $page_links['base_url'].$page_links['previous_page'] . '"class="btn btn-info" id="PrevPage" rel="tooltip" data-original-title="Alt + P"><i class="fa fa-backward"></i> Prev</a> ';
						}
						else {
							$buffer .= '<a href="#" class="btn btn-info disabled"><i class="fa fa-backward"></i> Prev</a> ';
						}

						$buffer .= "<select id=\"PageList\" class=\"select2\" rel=\"tooltip\" data-original-title=\"Jump directly to a page.\" onchange=\"javascript: window.location = '" . $page_links['base_url'] . "' + this.value;\">";
						foreach ($page_links['pages'] as $record_no => $page_no) {
							if ($page_no == '---') {
								$buffer .= "<option disabled=\"true\">$page_no</option>";
								continue;
							}
							$buffer .= '<option value="' . $record_no . '"' . ($page_no == $page_links['current_page'] ? ' selected="selected"' : null) . ">$page_no</option>";
						}
						$buffer .= '</select>  ';

						if (isset($page_links['next_page'])) {
							$buffer .= '<a href="' . $page_links['base_url'].$page_links['next_page'] . '"class="btn btn-info" id="NextPage" rel="tooltip" data-original-title="Alt + N"><i class="fa fa-forward"></i> Next</a> ';
						}
						else {
							$buffer .= '<a href="#" class="btn btn-info disabled"><i class="fa fa-forward"></i> Next</a> ';
						}

						if (isset($page_links['last_page'])) {
							$buffer .= ' <a href="' . $page_links['base_url'].$page_links['last_page'] . '"class="btn btn-info" id="LastPage" rel="tooltip" data-original-title="Alt + L"><i class="fa fa-step-forward"></i> Last</a> ';
						}
						else {
							$buffer .= '<a href="#" class="btn btn-info disabled"><i class="fa fa-step-forward"></i> Last</a> ';
						}
						$buffer .= '</div></div>';
					}
				}

				echo $buffer;
			?>

		</div>
	</div>

	<div class="card-body table-responsive p-0">
		<table id="mainTable" class="<?php echo  (isset($list['table']) ? $list['table'] : 'table table-head-fixed table-striped table-hover table-rowselect') ?>">


			<thead>
				<tr>
					<th>Job No</th>
					<th>Line</th>
					<th>Booking No</th>
					<th>Shipper Name</th>
					<th><span class="orange">SB No</span></th>
					<th><span class="red">Invoice No</span></th>
					<th>Containers</th>
					<th>Loading Port</th>
					<th>Gateway Port</th>
					<th>POD</th>
					<th>FPD</th>
					<th>Cargo Type</th>
					<th>Shipment Type</th>
					<th>Sub Type</th>
					<th>Stuffing</th>
					<th>Status</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th>Job No</th>
					<th>Line</th>
					<th>Booking No</th>
					<th>Shipper Name</th>
					<th><span class="orange">SB No</span></th>
					<th><span class="red">Invoice No</span></th>
					<th>Containers</th>
					<th>Loading Port</th>
					<th>Gateway Port</th>
					<th>POD</th>
					<th>FPD</th>
					<th>Cargo Type</th>
					<th>Shipment Type</th>
					<th>Sub Type</th>
					<th>Stuffing</th>
					<th>Status</th>
				</tr>
			</tfoot>

			<tbody>
			<?php
				foreach($rows as $r) {
					echo '<tr>
				<td class="nowrap">' . anchor($this->_clspath.$this->_class."/edit/".$r['id'], $r['id2_format']) . '</td>
				<td>' . $r['line_code'] . '</td>
				<td>' . $r['booking_no'] . '</td>
				<td>' . $r['shipper_name'] . '</td>
				<td><span class="orange">' . $r['sb_no'] . '</span></td>
				<td><span class="red">' . $r['invoice_no_date'] . '</span></td>
				<td class="aligncenter">' . $r['containers'] . '</td>
				<td>' . $r['custom_port'] . '</td>
				<td>' . $r['custom_port'] . '</td>
				<td>' . $r['discharge_port'] . '</td>
				<td>' . $r['fpod'] . '</td>
				<td>' . $r['cargo_type'] . '</td>
				<td class="aligncenter"><span class="label ' . $label_class[$r['shipment_type']] . '">' . $r['shipment_type'] . '</span></td>
				<td class="nowrap">';
					$values = explode(',', $r['sub_type']);
					foreach ($values as $v) {
						echo '<span class="label ' . $label_class[$v] . '">' . substr($v, 0, 1) . '</span> ';
					}

				echo '<td class="aligncenter"><span class="label ' . $label_class[$r['stuffing_type']] . '">' . $r['stuffing_type'] . '</span></td>
				<td class="aligncenter"><span class="label ' . $label_class[$r['status']] . '">' . $r['status'] . '</span></td>
			</tr>';
			}
			?>
			</tbody>
		</table>
	</div>
</div>