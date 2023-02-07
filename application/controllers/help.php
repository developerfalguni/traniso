<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help extends CI_Controller {
	var $_path;

	function __construct() {
		parent::__construct();

		$this->_path = FCPATH . 'docs/';
		$this->load->helper('file');
		$this->parsedown = new Parsedown();
	}

	function index() {
		$tree        = $this->_directory_tree('', $this->_path, array('folders' => array('images'), 'files' => array()));
		$data['nav'] = $this->_navigation($tree, $this->_path, base_url('help/index'));
		$segments    = $this->uri->segment_array();
		$filepath    = $tree;
		foreach ($segments as $s) {
			if ($s == 'help' OR $s == 'index') continue;
			$filepath = $filepath[$s];
		}
		$file            = read_file($this->_path.$filepath);
		$data['content'] = $this->parsedown->text($file);
		$data['title']   = $this->_clean_url($filepath, 'Title');
		$this->load->view('help', $data);
	}

	//  Recursively add files & directories to Tree
	function _directory_tree($parent, $dir, $ignore = null) {
		if (is_null($ignore))
			$ignore = array('folders' => array(), 'files' => array());
		
		$tree = array();
		$item = array_diff(scandir($dir), array(".", ".."));
		foreach ($item as $key => $value) {
			if (is_dir($dir . '/' . $value)) {
				if (! in_array($value, $ignore['folders']))
					$tree[$this->_clean_url($value, 'Filename')] = $this->_directory_tree($value, $dir . '/' . $value, $ignore);
			}
			else if (! in_array($value, $ignore['files'])) {
				if (substr($value, -3) === ".md") {
					$tree[$this->_clean_url($value, 'Filename')] = ($parent== '' ? '' : $parent . '/') . $value;
				}
			}
		}
		return $tree;
	}

	//  File to URL
	function _clean_url($url, $mode = 'Static') {
		switch ($mode) {
			case 'Static':
				$url = str_replace(".md", ".html", $url);
				$remove = array($this->_path . '/');
				$url = str_replace($remove, "", $url);
				$url = explode('/', $url);
				foreach ($url as &$a) {
					$a = explode('_', $a);
					if (isset($a[0]) && is_numeric($a[0])) unset($a[0]);
					$a = implode('_', $a);
				}
				$url = strtolower(implode('/', $url));
				return $url;

			case 'Title':
			case 'Filename':
				$parts = array_reverse(explode('/', $url));
				if (isset($parts[0])) {
					if ($parts[0] === "index.md" && isset($parts[1])) $url = $parts[1];
					else $url = $parts[0];
				}
				$url = explode('_', $url);
				if (isset($url[0]) && is_numeric($url[0])) unset($url[0]);
				if ($mode === 'Filename') 
					$url = strtolower(implode('_', $url));
				else 
					$url = humanize(implode(' ', $url));
				$url = str_replace(array(".md", ".html"), "", $url);
				return $url;
		}
	}

	function _navigation($tree, $current_dir, $url) {
		$return = '';
		foreach ($tree as $key => $node) {
			if (is_array($node)) {
				$link    = $url . '/' . $this->_clean_url($key, 'Filename');
				$return .= '<li>' . anchor($link, $this->_clean_url($key, 'Title'), 'class="disabled"');
				$dir     = ($current_dir === '') ? $key : $current_dir . '/' . $key;
				$return .= '<ul>' . $this->_navigation($node, $dir, $link) . '</ul></li>';
			}
			else {
				$link    = $url . '/' . $this->_clean_url($node, 'Filename');
				$return .= '<li>' . anchor($link, $this->_clean_url($key, 'Title'), 'class=""') . '</li>';
			}
		}
		return $return;
	}
}
