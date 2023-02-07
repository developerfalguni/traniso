<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Profiler extends CI_Profiler {

	public function __construct() {
		parent::__construct();
	}

	protected function _compile_benchmarks() {
		$profile = [];
		foreach ($this->CI->benchmark->marker as $key => $val) {
			// We match the "end" marker so that the list ends
			// up in the order that it was defined
			if (preg_match('/(.+?)_end$/i', $key, $match)
				&& isset($this->CI->benchmark->marker[$match[1].'_end'], $this->CI->benchmark->marker[$match[1].'_start'])) {
				$profile[$match[1]] = $this->CI->benchmark->elapsed_time($match[1].'_start', $key);
			}
		}

		foreach ($profile as $key => $val) {
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output[$key] = $val;
		}

		return $output;
	}

	protected function _compile_queries() {
		$dbs = [];

		// Let's determine which databases are currently connected to
		foreach (get_object_vars($this->CI) as $name => $cobject) {
			if (is_object($cobject)) {
				if ($cobject instanceof CI_DB) {
					$dbs[get_class($this->CI).':$'.$name] = $cobject;
				}
				elseif ($cobject instanceof CI_Model) {
					foreach (get_object_vars($cobject) as $mname => $mobject) {
						if ($mobject instanceof CI_DB) {
							$dbs[get_class($cobject).':$'.$mname] = $mobject;
						}
					}
				}
			}
		}

		if (count($dbs) === 0) {
			return ['queries' => $this->CI->lang->line('profiler_no_db')];
		}

		$count  = 0;

		foreach ($dbs as $name => $db) {
			$total_time = number_format(array_sum($db->query_times), 4).' '.$this->CI->lang->line('profiler_seconds');

			$output[$db->database] = 'Queries: '.count($db->queries).' ('.$total_time.')';

			if (count($db->queries) === 0) {
				$output['queries'] = $this->CI->lang->line('profiler_no_queries');
			}
			else {
				foreach ($db->queries as $key => $val) {
					$time = number_format($db->query_times[$key], 4);

					$output['queries'][] = ['time' => $time, 'query' => $val];
				}
			}
			$count++;
		}

		return $output;
	}

	protected function _compile_get() {
		if (count($_GET) === 0) {
			$output['get'] = $this->CI->lang->line('profiler_no_get');
		}
		else {
			foreach ($_GET as $key => $val) {
				is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, config_item('charset'))."'";
				$val = (is_array($val) OR is_object($val))
					? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));

				$output['get'][$key] = $val;
			}
		}

		return $output;
	}

	protected function _compile_post() {
		if (count($_POST) === 0 && count($_FILES) === 0) {
			$output['post'] = $this->CI->lang->line('profiler_no_post');
		}
		else {
			foreach ($_POST as $key => $val) {
				is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, config_item('charset'))."'";
				$val = (is_array($val) OR is_object($val))
					? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));

				$output['post'][$key] = $val;
			}

			foreach ($_FILES as $key => $val) {
				is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, config_item('charset'))."'";
				$val = (is_array($val) OR is_object($val))
					? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));

				$output['post'][$key] = $val;
			}
		}

		return $output;
	}

	protected function _compile_uri_string() {
		return ['uri' => ($this->CI->uri->uri_string === '' ? $this->CI->lang->line('profiler_no_uri') : $this->CI->uri->uri_string)];
	}

	protected function _compile_controller_info() {
		return ['controller' => $this->CI->router->class.'/'.$this->CI->router->method];
	}

	protected function _compile_memory_usage() {
		return ['memory' => (($usage = memory_get_usage()) != '' ? number_format($usage).' bytes' : $this->CI->lang->line('profiler_no_memory'))];
	}

	protected function _compile_http_headers() {
		foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD',' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR', 'HTTP_DNT') as $header) {
			$val = isset($_SERVER[$header]) ? htmlspecialchars($_SERVER[$header], ENT_QUOTES, config_item('charset')) : '';
			$output['http'][$header] = $val;
		}

		return $output;
	}

	protected function _compile_config() {
		foreach ($this->CI->config->config as $config => $val) {
			$output['config'][$config] = $val;
		}

		return $output;
	}

	protected function _compile_session_data() {
		if ( ! isset($this->CI->session)) {
			return;
		}

		$output['session'] = [];
		foreach ($this->CI->session->userdata() as $key => $val) {
			$output['session'][$key] = $val;
		}

		return $output;
	}

	public function run() {
		$output = [];
		$fields_displayed = 0;

		foreach ($this->_available_sections as $section) {
			if ($this->_compile_{$section} !== FALSE) {
				$func = '_compile_'.$section;
				ChromePhp::log($this->{$func}());
				// $output += $this->{$func}();
				$fields_displayed++;
			}
		}

		if ($fields_displayed === 0) {
			ChromePhp::log($this->CI->lang->line('profiler_no_profiles'));
		}
	}
}
