<?php
echo "<table class=\"table toolbar\"><tr>";

if((isset($show_search) ? $show_search : FALSE)) {
	echo '<td width="30%">' .
		form_open($this->uri->uri_string(), 'class="form-search"') .
		form_hidden(array('search_form' => 1)) . 
		'<div class="input-group">
			<input type="text" class="form-control form-control-sm Focus" name="search" value="' . (isset($search) ? $search : '') . '" id="Search">
			<span class="input-group-btn">
				<button class="btn btn-primary" type="submit" id="SearchButton"><i class="fa fa-search"></i> Search</button>
			</span>
		</div>
		</form></td>';
}

if (isset($buttons)) {
	echo '<td>';
	foreach ($buttons as $btn)
		echo $btn . '&nbsp;';
	echo '</td>';
}

if (isset($this->pagination)) {
	$page_links = $this->pagination->create_select_links();
	if ($page_links != FALSE) {
		// A Blank td to align properly
		echo '<td></td>
		<td class="paginator"><ul class="pager form-inline">';
		if (isset($page_links['first_page'])) {
			echo '<li><a href="' . $page_links['base_url'].$page_links['first_page'] . '" id="FirstPage" rel="tooltip" data-original-title="Ctrl + Up">First</a> <li>';
		}
		else { echo '<li class="disabled"><a href="#">First</a> <li>'; }

		if (isset($page_links['previous_page'])) {
			echo '<li><a href="' . $page_links['base_url'].$page_links['previous_page'] . '" id="PrevPage" rel="tooltip" data-original-title="Ctrl + Left">Prev</a></li>';
		}
		else { echo '<li class="disabled"><a href="#">Prev</a></li>'; }

		echo "<li> <select class=\"form-control\" id=\"PageList\" rel=\"tooltip\" data-original-title=\"Jump directly to a page.\" onchange=\"javascript: window.location = '" . $page_links['base_url'] . "' + this.value;\">";
		foreach ($page_links['pages'] as $record_no => $page_no) {
			if ($page_no == '---') {
				echo "<option disabled=\"true\">$page_no</option>";
				continue;
			}
			echo '<option value="' . $record_no . '"' . ($page_no == $page_links['current_page'] ? ' selected="selected"' : null) . ">$page_no</option>";
		}
		echo '</select> </li>';

		if (isset($page_links['next_page'])) {
			echo '<li><a href="' . $page_links['base_url'].$page_links['next_page'] . '" id="NextPage" rel="tooltip" data-original-title="Ctrl + Right">Next</a></li>';
		}
		else { echo '<li class="disabled"><a href="#">Next</a></li>'; }

		if (isset($page_links['last_page'])) {
			echo '<li> <a href="' . $page_links['base_url'].$page_links['last_page'] . '" id="LastPage" rel="tooltip" data-original-title="Ctrl + Down">Last</a></li>';
		}
		else { echo '<li class="disabled"> <a href="#">Last</a></li>'; }
		echo '</ul></td>';
	}
}
?>
</tr>
</table>

<table class="table table-condensed table-striped table-rowselect">
<thead>
<tr>
	<th>ID</th>
	<th>Date</th>
	<th>LR No.</th>
	<th>Party Ref.</th>
	<th>Registration No</th>
	<th>Party Name</th>
	<th>Transporter</th>
	<th>Container No</th>
	<th>Container Size</th>
	<th>From</th>
	<th>To</th>
	<th>Advance</th>
	<th>Fuel</th>
	<th>Remarks</th>
</tr>
</thead>

<tfoot>
<tr>
	<th>ID</th>
	<th>Date</th>
	<th>LR No.</th>
	<th>Party Ref.</th>
	<th>Registration No</th>
	<th>Party Name</th>
	<th>Transporter</th>
	<th>Container No</th>
	<th>Container Size</th>
	<th>From</th>
	<th>To</th>
	<th>Advance</th>
	<th>Fuel</th>
	<th>Remarks</th>
</tr>
</tfoot>

<tbody>
<?php
	foreach($rows as $r) {
		echo '<tr>
	<td class="tiny">' . anchor($this->_clspath.$this->_class."/edit/".$r['id'], $r['id']) . '</td> 
	<td class="tiny">' . $r['date'] . '</td>
	<td class="tiny">' . $r['lr_no'] . '</td>
	<td class="tiny">' . $r['party_reference_no'] . '</td>
	<td>' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['transporter_name'] . '</td>
	<td class="tiny">' . $r['container_no'] . '</td>
	<td class="tiny">' . $r['container_size'] . '</td>
	<td class="tiny">' . $r['from_location'] . '</td>
	<td class="tiny">' . $r['to_location'] . '</td>
	<td class="tiny">' . $r['advance'] . '</td>
	<td class="tiny">' . $r['fuel'] . '</td>
	<td class="tiny">' . $r['remarks'] . '</td>
	
</tr>';
}
?>
</tbody>
</table>