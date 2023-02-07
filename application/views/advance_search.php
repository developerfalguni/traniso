<div class="card-header">

	<div class="row mb-2">
        <?php 
            $search_tags = [];
            foreach($search_fields as $f => $n) {

                //$search_tags[] = '<a class="dropdown-item SearchTags" href="#" data-field="' . $f . ':">' . humanize($f) . '</a>';
                //$fields = array_unique(array_column($list['rows'], $f));
                if($f == 'id' OR $f == 'actualDist' OR $f == 'noValidDays' OR $f == 'extValidity' OR $f == 'ewbNo' OR $f == 'vehicleNo' OR $f == 'ownStatus' OR $f == 'VehicleType' OR $f == 'trackingUser')
                    continue;

                if($f == 'fromTrdName' OR $f == 'toTrdName')
                    echo '<div class="col-lg-6">';  
                else
                    echo '<div class="col-lg-3">';

                echo '<div class="mb-2">';

                    if (isset($headername))
                        echo '<label class="form-label">'. $headername[$f] .'</label>';
                    else
                        echo '<label class="form-label">'. humanize($f) .'</label>';
                    

                    if($f == 'ewayBillDate' OR $f == 'reachDate' OR $f == 'unloadDate' OR $f == 'validUpto')
                        echo form_input($f, (isset($parsed_search[$f]) ? $parsed_search[$f] : ''), 'class="form-control AdvancedSearch"', 'date');
                    else{
                        
                        echo '<div class="input-group btn-group">';
                        echo '<div class="dropdown btn-group">
                            <button class="btn btn-secondary dropdown-toggle pt-0 pb-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-filter-menu font-size-16"></i>
                            </button>
                            <div class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
                                <label class="form-label">Filter Method</label>';
                                echo form_dropdown('filter'.$f, getFilterable(), (isset($parsed_filter['filter'.$f]) ? $parsed_filter['filter'.$f] : ''), 'class="form-select FilterSearch" id="Filter'.$f.'"');
                            echo '</div>
                        </div>';

                        $options = [(isset($parsed_search[$f]) ? $parsed_search[$f] : '') => (isset($parsed_search[$f]) ? $parsed_search[$f] : '')];

                        echo form_dropdown($f, $options, (isset($parsed_search[$f]) ? $parsed_search[$f] : ''), 'class="SelectizeKaabar form-control AdvancedSearch" id="'.strtolower($f).'"');
                        echo '</div>';
                    }

                echo '</div>';    
                echo '</div>';    
                
            } 
        ?>
    </div>
<!-- <?php //echo form_open($this->uri->uri_string()); ?>
<span class="bold">From : <input type="text" class="DateTime" name="from_date" value="<?php //echo $from_date ?>" size="10" />
To : <input type="text" class="DateTime" name="to_date" value="<?php //echo $to_date ?>" size="10" />
<?php //echo form_dropdown('search_in', $search_list, $search_in) ?>&nbsp;
<input type="text" name="search" value="<?php //echo $search ?>" size="10" id="Search" /></span>
<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>&nbsp;
<?php //echo anchor($this->_clspath.$this->_class.'/preview/', 'Preview', 'class="form-control form-control-sm"') ?>&nbsp;
<?php //echo anchor($this->_clspath.$this->_class.'/excel', 'Excel', 'class="btn btn-default"') ?> -->
</form>
</div>