<?php

use League\Flysystem\Dropbox\DropboxAdapter;
use League\Flysystem\Filesystem;
use Dropbox\Client;

class Backup {
	function __construct() {
		$this->_ci =& get_instance();
	}
	
	function run() {
		$client     = new Client('dwnrzgchFCkAAAAAAAAAtlABnkXWaxrA6zoXEwlo0-YbeFAfVC6g5_XuVy4bnwAU', '7ulwridz5zxjolw');
		$adapter    = new DropboxAdapter($client);
		$filesystem = new Filesystem($adapter);

		$project = $this->_ci->db->database;

		if (! $filesystem->has($project)) {
			$filesystem->createDir($project);
		}

		$latest_ctime    = 0;
		$latest_filename = '';
		$backup_path = FCPATH.'backup';
		$d = dir($backup_path);
		while (false !== ($entry = $d->read())) {
			$filepath = "{$backup_path}/{$entry}";
			if (is_file($filepath) && filectime($filepath) > $latest_ctime) {
				$latest_ctime    = filectime($filepath);
				$latest_filename = $entry;
			}
		}

		$this->_ci->load->helper('file');
		$contents = read_file("{$backup_path}/{$latest_filename}");
		$filesystem->write("{$project}/{$project}_".date('Y_m_d_His').'.gz', $contents);
	}
}