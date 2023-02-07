<?php
$buffer = null;


$buffer .= '<div class="card">
	      		<div class="card-header">
	        		<h3 class="card-title"><span class="">'. anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>') .'</span> '. strtoupper($page_title) .'</h3>
	        		<div class="card-tools">
	          			<ol class="breadcrumb float-sm-right m-0">
	              			<li class="breadcrumb-item"><a href="#">'. anchor('main','Dashboard') .'</a></li>
	              			<li class="breadcrumb-item active mr-1">'. humanize(clean($this->_clspath).' / '.$this->_class).'</li>
	            		</ol>
	        		</div>
	        	</div>
        		<div class="card-body p-2">';

				if(isset($list['preload_page'])) {
					$buffer .= $this->load->view($list['preload_page'], $list, true);
				}
				else
				{

					$buffer .= '<div class="row p-1 mb-2">';

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
						
						if(isset($list['search_form'])) {
							$buffer .= '<div class="col-xs-8 col-sm-3 col-md-3">' . $this->load->view($list['search_form'], null, true) . '</div>';
						}

						if (isset($buttons)) {
							$buffer .= '<div class="col-xs-4 col-sm-3 col-md-8">';
							foreach ($buttons as $btn)
								$buffer .= $btn;
							$buffer .= '</div>';
						}

						if (isset($this->pagination)) {
							$page_links = $this->pagination->create_select_links();

							

							if ($page_links != FALSE) {
								$buffer .= '<div class="col-xs-12 col-sm-6 col-md-4">';

								$buffer .= '<div class="btn-group float-right">';

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

					$buffer .= '</div>';

				}

				if(isset($list_title) OR isset($sparkline)){
					$buffer .= '<div class="row p-3 mb-2">';
						if (isset($list_title))
							$buffer .= '<div class="col-md-3"><h3>'.$list_title.'</h3></div>';

						if (isset($sparkline)) {
							$buffer .= '<div class="col-md-2">' . $sparkline . '</div>';
						}

					$buffer .= '</div>';
				}

				$buffer .= '<div class="row">
		        				<div class="table-responsive p-0 pt-2">';
								
								$buffer .= '<table id="mainTable" class="' . (isset($list['table']) ? $list['table'] : 'table table-head-fixed table-hover table-rowselect') . '">';


								$thead = '<tr>';
								foreach($list['heading'] as $value) {
									$thead .= "<th>$value</th>";
								}
								$thead .= '</tr>';

								$buffer .= '<thead>' . $thead . '</thead>
									<tfoot>' . $thead . '</tfoot>
									<tbody>';

								if (empty($list['data'])) {
									$buffer .= "<tr><td colspan=\"" . count($list['heading']) . "\"><div class=\"alert alert-danger\">No Records Found.</div></td></tr>";
								}

								foreach($list['data'] as $row) {
									$buffer .= "<tr id=".$row['id'].">";
									
									foreach ($row as $field => $value) {
										$anchor_class = (isset($list['class'][$field]['anchor_class']) ? 'class="'.$list['class'][$field]['anchor_class'].'"' : '');

										if (isset($list['link_col']) &&
										   ! is_array($list['link_col']) &&
										   strlen($list['link_col']) > 0 &&
										   $list['link_col'] == $field) {
										   	if (is_array($list['class'][$field]))
										   		$buffer .= "<td class=\"" . $list['class'][$field]['class'] . "\">" . anchor(underscore($list['link_url'] . $row[$list['class'][$field]['link']]), $value, $anchor_class) . "</td>\n";
										   	else
												$buffer .= "<td class=\"" . $list['class'][$field] . "\">" . anchor(underscore($list['link_url'] . $value), $value, $anchor_class) . "</td>\n";
										}
										else if(isset($list['link_col']) &&
												is_array($list['link_col']) &&
												in_array($field, $list['link_col']) &&
												strlen($value) > 0) {
											if (is_array($list['class'][$field]))
										   		$buffer .= "<td class=\"" . $list['class'][$field]['class'] . "\">" . anchor(underscore($list['link_url'] . $row[$list['class'][$field]['link']]), $value, $anchor_class) . "</td>\n";
										   	else
												$buffer .= "<td class=\"" . $list['class'][$field] . "\">" . anchor(underscore($list['link_url'][$field] . $value), $value, $anchor_class) . "</td>\n";
										}
										else {
											if (isset($list['class'][$field])) {
												$buffer .= "<td class=\"" . $list['class'][$field] . "\">";
												if (in_array('Rupee', explode(' ', $list['class'][$field]))) {
													$value = inr_format($value);
												}
												if (in_array('Label', explode(' ', $list['class'][$field]))) {
													if (in_array('Multiple', explode(' ', $list['class'][$field]))) {
														$values = explode(',', $value);
														foreach ($values as $v) {
															$buffer .= '<span class="label ' . $label_class[$v] . '">' . $v . '</span> ';
														}
													}
													else
														$buffer .= '<span class="label ' . $label_class[$value] . '">' . $value . '</span>';
												}
												else if ($list['class'][$field] == "Image") {
													$buffer .= '<img src="' . base_url("images/" . $value . ".png") . '" />';
												}
												else {
													$buffer .= $value;
												}
												$buffer .= "</td>\n";
											}
										}
										
										if (isset($list['show_total'][$field])) {
											$list['show_total'][$field] += $value;
										}
									}
									$buffer .= "</tr>";
								}

								if (isset($list['show_total']) && !empty($list['data'])) {
									$buffer .= "<tr>";
									foreach ($list['class'] as $field=>$class) {
										if (isset($list['show_total'][$field])) {
											$buffer .= "<td class=\"bold " . $list['class'][$field] . "\">" . number_format((double)$list['show_total'][$field], 2, '.', '') . "</td>";
										}
										else {
											$buffer .= "<td></td>";
										}
									}
									$buffer .= "</tr>";
								}

								$buffer .= "</tbody></table>";
							$buffer .= "</div>";
						$buffer .= "</div>";
					$buffer .= "</div>";
				$buffer .= "</div>";
			
			$buffer .= "</div>";
$buffer .= "</div>";
	


// If Toolbar is Empty.. Remove it.
echo str_replace('<table class="table toolbar"><tr><td></td></tr></table>', '', $buffer);
