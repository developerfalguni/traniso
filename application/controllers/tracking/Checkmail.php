<?php

class Checkmail extends MY_Controller {
	var $_folder;

	function __construct() {
		parent::__construct();

		$this->_folder  = FCPATH . 'share/';
		$this->load->helper('filelist');
	}	

	function index() {
		$this->load->library('imap');
		$this->imap->open(Settings::get('imap_host'), Settings::get('imap_port'));
		$this->imap->setAuthentication(Settings::get('imap_user'), Settings::get('imap_password'));
		$messages = $this->imap->search("UNSEEN");
		foreach ($messages as $message) {
			$attachments = $message->getAttachments();
			foreach ($attachments as $attachment) {
				$attachment->saveToDirectory($this->_folder);
			}
		}

		$this->update_be_ack();
		$this->update_be();
		
		$this->update_sb_ack();
	}

	function update_be() {
		$this->load->model('import');
		$job_path = FCPATH . 'documents/jobs/';
		$pending  = getFileList($this->_folder, array('prn'));
		foreach ($pending as $f) {
			$content = read_file($f['server_path']);
			if ($content) {
				$be 		= false;
				$be_no 		= false;
				$be_date 	= false;
				$bl_no 		= false;
				$bl_date 	= false;

				// Reading BE No and Date
				$start = strpos($content, "BE No/Dt./cc/Typ:");
				if ($start) {
					$start += 17;
					$count = strpos(substr($content, $start), "\n");
					$be    = explode("/", trim(substr($content, $start, $count)));
					if (count($be) > 0) {
						$be_no 	 = $be[0];
						$be_date = $be[1] . '-' . $be[2] . '-' . $be[3];
					}
				}

				// Reading BL No and Date
				$start = strpos($content, "BL   No       :");
				if ($start) {
					$start += 16;
					$count = strpos($content, "H/BL No         :") - $start;
					$bl_no = trim(substr($content, $start, $count));

					$start = strpos($content, "Date          :");
					if ($start) {
						$start   += 16;
						$bl_date = str_replace('/', '-', trim(substr($content, $start, 10)));
					}
				}

				// If everything OK than save
				if ($be_no && $be_date && $bl_no && $bl_date) {
					$attach_be = false;
					$query = $this->db->query("SELECT id, type, cargo_type, product_id 
						FROM jobs WHERE bl_no = ? AND bl_date = ? AND LENGTH(be_no) > 0", array($bl_no, convDate($bl_date)));
					$row = $query->row_array();
					if ($row) {
						$this->kaabar->save('jobs', array('be_no' => $be_no, 'be_date' => $be_date), array('id' => $row['id']));
						$attach_be = true;
					}
					else {
						$query = $this->db->query("SELECT id, type, cargo_type, product_id 
							FROM jobs WHERE be_no = ? AND be_date = ?", array($be_no, convDate($be_date)));
						$row = $query->row_array();
						if ($row)
							$attach_be = true;
					}

					if ($attach_be) {
						// After saving, Check if already attached, then delete, else attach in documents.
						$query = $this->db->query("SELECT id FROM document_types WHERE code = 'BE' AND type = ? AND cargo_type = ? AND product_id = ?", 
							array($row['type'], $row['cargo_type'], $row['product_id'])
						);
						$document_type = $query->row_array();
						if ($document_type) {
							$query = $this->db->query("SELECT * FROM attached_documents WHERE job_id = ? AND document_type_id = ?", 
								array($row['id'], $document_type['id'])
							);
							$document = $query->row_array();
							if (! $document) {
								$docdir = $this->import->getDocFolder($job_path, $row['id']);
								rename($f['server_path'], $job_path.$docdir.basename($f['server_path']));
								$this->db->insert('attached_documents', array(
									'job_id'           => $row['id'],
									'document_type_id' => $document_type['id'],
									'file'             => basename($f['server_path'])
								));
							}
							else {
								$docdir = $this->import->getDocFolder($job_path, $row['id']);
								rename($f['server_path'], $job_path.$docdir.basename($f['server_path']));
								$this->db->update('attached_documents', 
									array('file' => basename($f['server_path'])), 
									array('id'   => $document['id'])
								);
							}
						}
					}
				}
			}
		}
	}

	function update_be_ack() {
		$pending  = getFileList($this->_folder, array('ack_'));
		foreach ($pending as $f) {
			$content = read_file($f['server_path']);
			if ($content) {
				$vi_no 		= false;
				$vi_date 	= false;
				$be_no 		= false;
				$be_date 	= false;

				// Reading VI Job No and Date & SB No and Date
				$lines   = explode("\r\n", $content);
				$fields  = explode(chr(29), $lines[1]);
				$vi_no   = $fields[2];
				$vi_date = $fields[3];
				$be_no   = $fields[4];
				$be_date = $fields[5];

				// If everything OK than save
				if (count($fields) == 10 && $vi_no && $vi_date && $be_no && $be_date) {
					$this->db->query("UPDATE jobs SET be_no = ?, be_date = ? WHERE vi_job_no = ? AND LENGTH(be_no) = 0", array(
						$be_no, substr($be_date, 4, 4).'-'.substr($be_date, 2, 2).'-'.substr($be_date, 0, 2),
						'IMP/'. str_pad($vi_no, 5, '0', STR_PAD_LEFT).'/'.str_replace("_", "-", $this->kaabar->getFinancialYear(date('Y-m-d')))
					));
					unlink($f['server_path']);
				}
			}
		}
	}

	function update_sb_ack() {
		$pending  = getFileList($this->_folder, array('ack'));
		foreach ($pending as $f) {
			$content = read_file($f['server_path']);
			if ($content) {
				$vi_no 		= false;
				$vi_date 	= false;
				$sb_no 		= false;
				$sb_date 	= false;

				// Reading VI Job No and Date & SB No and Date
				$lines   = explode("\r\n", $content);
				$fields  = explode(chr(29), $lines[1]);
				$vi_no   = $fields[1];
				$vi_date = $fields[2];
				$sb_no   = $fields[3];
				$sb_date = $fields[4];

				// If everything OK than save
				if (count($fields) == 5 && $vi_no && $vi_date && $sb_no && $sb_date) {
					$this->db->update('jobs', 
						array('sb_no'     => $sb_no, 'sb_date' => date('Y-m-d', strtotime($sb_date))), 
						array('vi_job_no' => 'EXP/'. str_pad($vi_no, 5, '0', STR_PAD_LEFT).'/'.str_replace("_", "-", $this->kaabar->getFinancialYear(date('Y-m-d'))))
					);
					unlink($f['server_path']);
				}
			}
		}
	}
}
