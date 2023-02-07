<?php

define('ESCAPE', chr(27));

// Printer Class for Dot Matrix Printers
class Printer {
	const 	PREVIEW   = 1;
	const 	DOWNLOAD  = 2;
	const 	SAVEFILE  = 3;
	const 	PRINTFILE = 4;

	const 	PORTRAIT  = 1;
	const 	LANDSCAPE = 2;
	static 	$_ci  = '';
	static 	$_tmp = '';
	var 	$_printer;
	var 	$_buffer;

	function __construct() {
		self::$_ci =& get_instance();
		self::$_tmp = FCPATH . 'tmp/';

		$this->_printer = array(
			'name'        => '', 
			'page_size'   => 'a4', 
			'copies'      => 1, 
			'orientation' => self::PORTRAIT
		);
	}

	function set_printer($printer) {
		foreach ($printer as $key => $value) {
			$this->_printer[$key] = $value;
		}
	}

	function _pipeExec($cmd, $input = '') {
		$proc = proc_open($cmd, array(0 => array('pipe','r'), 1 => array('pipe','w'), 2 => array('pipe','w')), $pipes);
		fwrite($pipes[0], $input);
		fclose($pipes[0]);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		$rtn = proc_close($proc);
		return array(
			'stdout' => $stdout,
			'stderr' => $stderr,
			'return' => $rtn
		);
	}

	function _getEscCode($empty = true) {
		if ($empty) {
			return array(
				'RESET'             => NULL,
				'DRAFT'             => NULL,
				'NLQ'               => NULL,
				'NLQ_ROMAN'         => NULL,
				'NLQ_SANS_SERIF'    => NULL,
				'CPI_10'            => NULL,
				'CPI_12'            => NULL,
				'CONDENSED'         => NULL,
				'EMPHASIZED'        => NULL,
				'DOUBLE_STRIKE'     => NULL,
				'DOUBLE_WIDTH'      => NULL,
				'ITALIC'            => NULL,
				'UNDERLINE'         => NULL,
				'DOUBLE_WIDTH_ON'   => NULL,
				'DOUBLE_WIDTH_OFF'  => NULL,
				'DOUBLE_STRIKE_ON'  => NULL,
				'DOUBLE_STRIKE_OFF' => NULL,
				'EMPHASIZED_ON'     => NULL,
				'EMPHASIZED_OFF'    => NULL,
				'SUPERSCRIPT_ON'    => NULL,
				'SUPERSCRIPT_OFF'   => NULL,
				'SUBSCRIPT_ON'      => NULL,
				'SUBSCRIPT_OFF'     => NULL,
				'ITALIC_ON'         => NULL,
				'ITALIC_OFF'        => NULL,
				'UNDERLINE_ON'      => NULL,
				'UNDERLINE_OFF'     => NULL,
			);
		}
		else {
			return array(
				'RESET'             => ESCAPE.'@',
				'DRAFT'             => ESCAPE.'x'.chr(0),
				'NLQ'               => ESCAPE.'x'.chr(1),
				'NLQ_ROMAN'         => ESCAPE.'k'.chr(0),
				'NLQ_SANS_SERIF'    => ESCAPE.'k'.chr(1),
				'CPI_10'            => ESCAPE.'!'.chr(0),
				'CPI_12'            => ESCAPE.'!'.chr(1),
				'CONDENSED'         => ESCAPE.'!'.chr(4),
				'EMPHASIZED'        => ESCAPE.'!'.chr(8),
				'DOUBLE_STRIKE'     => ESCAPE.'!'.chr(16),
				'DOUBLE_WIDTH'      => ESCAPE.'!'.chr(32),
				'ITALIC'            => ESCAPE.'!'.chr(64),
				'UNDERLINE'         => ESCAPE.'!'.chr(128),
				'DOUBLE_WIDTH_ON'   => ESCAPE.'W1',
				'DOUBLE_WIDTH_OFF'  => ESCAPE.'W0',
				'DOUBLE_STRIKE_ON'  => ESCAPE.'G',
				'DOUBLE_STRIKE_OFF' => ESCAPE.'H',
				'EMPHASIZED_ON'     => ESCAPE.'E',
				'EMPHASIZED_OFF'    => ESCAPE.'F',
				'SUPERSCRIPT_ON'    => ESCAPE.'S0',
				'SUPERSCRIPT_OFF'   => ESCAPE.'T',
				'SUBSCRIPT_ON'      => ESCAPE.'S1',
				'SUBSCRIPT_OFF'     => ESCAPE.'T',
				'ITALIC_ON'         => ESCAPE.'4',
				'ITALIC_OFF'        => ESCAPE.'5',
				'UNDERLINE_ON'      => ESCAPE.'1',
				'UNDERLINE_OFF'     => ESCAPE.'0'
			);
		}
	}

	function render($page, $data, $plain_text = false) {
		$this->_buffer = self::$_ci->load->view($page, $data + self::_getEscCode($plain_text), true);
	}

	function getBuffer() {
		return $this->_buffer;
	}

	function output($mode = self::PRINTFILE, $file) {
		switch ($mode) {
			case self::PREVIEW:
				echo '<html><body><pre>' . $this->_buffer . '</pre></body></html>';
			break;

			case self::DOWNLOAD:
				if (! headers_sent()) {
					header('Content-Description: File Transfer');
					header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
					header('Pragma: public');
					header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
					// force download dialog
					header('Content-Type: application/force-download');
					header('Content-Type: application/octet-stream', false);
					header('Content-Type: application/download', false);
					header('Content-Type: application/txt', false);
					// use the Content-Disposition header to supply a recommended filename
					header('Content-Disposition: attachment; filename="'.basename($file).'.txt";');
					header('Content-Transfer-Encoding: text');
					header('Content-Length: '.strlen($this->_buffer));
					echo $this->_buffer;
				}
			break;

			case self::SAVEFILE:
			case self::PRINTFILE:
				$filename = self::$_tmp . uniqid() . '.txt';
				file_put_contents($filename, $this->_buffer);

				echo '<html><body><pre>';

				if ($mode == self::PRINTFILE) {
					if (defined('LINUX')) {
						$cmd = "lp" . 
							" -d " . $this->_printer['name'] . 
							" -n " . $this->_printer['copies'] . 
							" -o media=" . $this->_printer['page_size'] .
							" $filename";
						
						$return = self::_pipeExec($cmd);
						log_message('debug', 'stdout: '.$return['stdout']);
						log_message('debug', 'stderr: '.$return['stderr']);
						log_message('debug', 'return: '.$return['return']);

						unlink($filename);
					}
					else {
						passthru("print" . 
							" /D:" . $this->_printer['name'] . 
							" " . str_replace('/', '\\', $filename), $return);
						unlink($filename);
					}

					echo "\n\n" . $this->_buffer . '</pre></body></html>';
				}
				break;

			default:
				throw new Exception('Printer: Invalid mode "' . htmlspecialchars($mode, ENT_QUOTES) . '".');
		}
	}
}
