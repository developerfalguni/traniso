<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {
	
	function __construct($params = array()) {
		parent::__construct($params);
	}
	
	function create_select_links() {
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0) {
		   return FALSE;
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1) {
			return FALSE;
		}

		// Determine the current page number.		
		$CI =& get_instance();
		if ($CI->uri->segment($this->uri_segment) != 0) {
			$this->cur_page = $CI->uri->segment($this->uri_segment);
			
			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		}
		
		if ( ! is_numeric($this->cur_page)) {
			$this->cur_page = 0;
		}
		
		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->total_rows) {
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}
		
		$uri_page_number = $this->cur_page;
		$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = 1;
		$end   = $num_pages;

		// Add a trailing slash to the base URL if needed
		$output['base_url'] = rtrim($this->base_url, '/') .'/';

		// Render the "First" link
		if  ($this->cur_page > 2) {
			$output['first_page'] = '0';
		}

		// Render the "previous" link
		if  (($this->cur_page - $this->num_links) >= 0) {
			$i = $uri_page_number - $this->per_page;
			if ($i == 0) $i = '0';
			$output['previous_page'] = $i;
		}

		if ($end > 50) {
			$nof_entries = 10;
			$mend = $nof_entries;
			for ($loop = $start -1; $loop <= $mend; $loop++) {
				$i = ($loop * $this->per_page) - $this->per_page;
				if ($i >= 0) {
					if ($this->cur_page == $loop) {
						$output['current_page'] = $loop; // Current page
						$mend = $loop + $nof_entries;
					}
					$output['pages'][$i] = $loop;
				}
			}

			if ($this->cur_page >= $nof_entries && $this->cur_page < ($end-$nof_entries)) {
				$mstart = $this->cur_page - $nof_entries;
				$mend = $this->cur_page + $nof_entries;
			}
			else {
				$mstart = round($end / 2, 0) - $nof_entries;
				$mend = round($end / 2, 0) + $nof_entries;
			}
			
			$loop = ($loop > $mstart) ? $loop : $mstart;
			$output['pages'][] = '...';
			for (; $loop <= $mend; $loop++) {
				$i = ($loop * $this->per_page) - $this->per_page;
				if ($i >= 0) {
					if ($this->cur_page == $loop) {
						$output['current_page'] = $loop; // Current page
					}
					$output['pages'][$i] = $loop;
				}
			}
			$output['pages'][] = '...';

			$mstart = $end - $nof_entries;
			if ($this->cur_page >= $mstart) {
				$mstart -= $nof_entries;
			}
			$loop = ($loop > $mstart) ? $loop : $mstart;
			for (; $loop <= $end; $loop++) {
				$i = ($loop * $this->per_page) - $this->per_page;
				if ($i >= 0) {
					if ($this->cur_page == $loop) {
						$output['current_page'] = $loop; // Current page
					}
					$output['pages'][$i] = $loop;
				}
			}
		}
		else {
			for ($loop = $start -1; $loop <= $end; $loop++) {
				$i = ($loop * $this->per_page) - $this->per_page;
						
				if ($i >= 0) {
					if ($this->cur_page == $loop) {
						$output['current_page'] = $loop; // Current page
					}
					$output['pages'][$i] = $loop;
				}
			}
		}

		// Render the "next" link
		if ($this->cur_page < $num_pages) {
			$output['next_page'] = ($this->cur_page * $this->per_page);
		}

		// Render the "Last" link
		if ($this->cur_page < $num_pages) {
			$i = (($num_pages * $this->per_page) - $this->per_page);
			$output['last_page'] = $i;
		}
		
		return $output;
	}
}
