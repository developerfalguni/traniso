<?php echo form_open(uri_string(), 'class="form-search" id="SearchForm"'); ?>
    <input type="hidden" name="search_form" value="1">
    <input type="hidden" name="advance_form" id="advanceForm" value="<?php echo (isset($advance_form) ? $advance_form : '') ?>">
    <input type="hidden" name="advance_filter_form" id="advanceFilterForm" value="<?php echo (isset($advance_filter_form) ? $advance_filter_form : '') ?>">

    <div class="row">
    	<div class="col-lg-3">
    		<label class="control-label">FROM DATE</label>
    		<div class="input-group">
				<?php echo form_input('from_date', $from_date, 'class="form-control DatePicker" maxlength="10"'); ?>
				<div class="input-group-append">
					<div class="input-group-text"><i class="icon-calendar"></i></div>
				</div>
			</div>
		</div>
		<div class="col-lg-3">
			<label class="control-label">TO DATE</label>
    		<div class="input-group">
				<?php echo form_input('to_date', $to_date, 'class="form-control DatePicker" maxlength="10"'); ?>
				<div class="input-group-append">
					<div class="input-group-text"><i class="icon-calendar"></i></div>
				</div>
			</div>
		</div>

        <?php 
            $search_tags = [];
            foreach($search_fields as $f => $n) {

				if($f == 'id' OR $f == 'job_date' OR $f == 'amount' OR $f == 'document' OR $f == 'project_costsheet' OR $f == 'actual_costsheet' OR $f == 'quotation' OR $f == 'reason_not_billed' OR $f == 'upload')
			    	continue;


			    echo '<div class="col-lg-3">';

			        if (isset($heading))
			            echo '<label class="control-label">'. strtoupper($heading[$f]) .'</label>';
			        else
			            echo '<label class="control-label">'. strtoupper($f) .'</label>';
			        
			        if($f == 'job_date'){
			        	echo '<div class="input-group input-group-sm">'.
			        		form_input($f, (isset($parsed_search[$f]) ? $parsed_search[$f] : ''), 'class="form-control DatePicker AdvancedSearch" maxlength="10"').
			        		'<div class="input-group-append">
								<div class="input-group-text"><i class="icon-calendar"></i></div>
							</div>
						</div>';
					}
			        else
			        {	
			        	echo '<div class="form-group">';
			        	echo form_input($f, (isset($parsed_search[$f]) ? $parsed_search[$f] : ''), 'class="form-control AdvancedSearch" id="'.strtolower($f).'"');
			        	echo '</div>';
			        }
			    echo '</div>';    
			} 
        ?>
    </div>
    <div class="row">
        <div class="col-lg-3 d-none">
            <div class="mb-3">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <input type="search" class="form-control main-search Focus" name="search" id="Search">
                    <button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="margin">
            	<button type="button" onclick="javascript: combineSearch()" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                <button type="button" class="btn btn-danger" onclick="javascript: clearSearch()"><i class="icon-refresh"></i> Reset</button>
            </div>
        </div>
        <?php if (isset($buttons)) {
			echo '<div class="col-lg-6">';
			foreach ($buttons as $btn)
				echo $btn;
			echo '</div>';
		} ?>
    	
		<?php
		$buffer = '';
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

		echo $buffer;
		?>
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function() {
		<?php foreach($search_fields as $f => $n) { 

			if($f == 'id' OR $f == 'job_date' OR $f == 'amount' OR $f == 'document' OR $f == 'project_costsheet' OR $f == 'actual_costsheet' OR $f == 'quotation' OR $f == 'reason_not_billed' OR $f == 'upload')
				    	continue; 
		?>

			$('#<?php echo strtolower($f) ?>').kaabar_autocomplete_full({source: '<?php echo site_url('export/pending/json/jobs/id/'.$f) ?>'});

		<?php } ?>
	});
</script>