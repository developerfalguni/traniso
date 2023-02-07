<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Initial_schema extends CI_Migration {

	public function up() {
		/*$this->dbforge->add_field("id  int NOT NULL AUTO_INCREMENT PRIMARY KEY");
		$this->dbforge->add_field("title varchar(100) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("content varchar(255) NOT NULL DEFAULT ''");

		$this->dbforge->create_table('posts', TRUE);
		$this->db->insert('posts', array('id' => 1, 'title' => 'Home', 'content' => 'Welcome to IDEX Solutions'));*/
	}

	public function down() {
		$this->dbforge->drop_table('posts');
	}
}
