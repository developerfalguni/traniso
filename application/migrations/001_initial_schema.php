<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Initial_schema extends CI_Migration {

	public function up() {
		// CI Session Table
		$this->db->query("CREATE TABLE IF NOT EXISTS ci_sessions (
			session_id varchar(40) NOT NULL DEFAULT '0',
			ip_address varchar(16) NOT NULL DEFAULT '0',
			user_agent varchar(120) NOT NULL,
			last_activity int(10) unsigned NOT NULL DEFAULT '0',
			user_data text NOT NULL,
			PRIMARY KEY (session_id),
			KEY last_activity_idx (last_activity)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

		// Captcha Table
		$this->db->query("CREATE TABLE IF NOT EXISTS captcha (
			captcha_id bigint(13) unsigned NOT NULL AUTO_INCREMENT,
			captcha_time int(10) unsigned NOT NULL,
			ip_address varchar(16) NOT NULL DEFAULT '0',
			word varchar(20) NOT NULL,
			PRIMARY KEY (captcha_id),
			KEY word (word)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

		// Auth Tables
		if (! $this->db->table_exists('contents')) {
			$this->db->query("CREATE TABLE contents (
				id int NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL default '',
				PRIMARY KEY  (id),
				UNIQUE KEY name (name)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

			$this->db->insert('contents', array('name' => '*');
		}

		if (! $this->db->table_exists('new_users')) {
			$this->db->query("CREATE TABLE new_users (
				id int NOT NULL AUTO_INCREMENT,
				created timestamp NOT NULL default CURRENT_TIMESTAMP,
				username varchar(50) NOT NULL default '',
				password varchar(32) NOT NULL default '',
				fullname varchar(50) NOT NULL default '',
				email varchar(255) NOT NULL default '',
				auth_code varchar(32) NOT NULL,
				PRIMARY KEY  (id),
				UNIQUE KEY username (username)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
		}

		if (! $this->db->table_exists('users')) {
			$this->db->query("CREATE TABLE users (
				id int NOT NULL AUTO_INCREMENT,
				created timestamp NOT NULL default CURRENT_TIMESTAMP,
				username varchar(20) NOT NULL default '',
				password varchar(32) NOT NULL default '',
				fullname varchar(50) NOT NULL default '',
				email varchar(255) NOT NULL default '',
				status ENUM('Active', 'Suspended', 'Disabled') NOT NULL DEFAULT 'Active',
				last_modified timestamp NOT NULL default '0000-00-00 00:00:00',
				last_login timestamp NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (id),
				UNIQUE KEY username (username)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
		
			$this->db->insert('users', array(
				'username'      => 'admin', 
				'password'      => _addSalt('a'), 
				'fullname'      => 'Mr. Administrator', 
				'email'         => 'admin@idexindia.com', 
				'last_modified' => '0000-00-00', 
				'last_login'    => '0000-00-00'
				)
			);
		}

		if (! $this->db->table_exists('groups')) {
			$this->db->query("CREATE TABLE groups (
				id int NOT NULL AUTO_INCREMENT,
				name varchar(50) NOT NULL default '',
				PRIMARY KEY  (id),
				UNIQUE KEY name (name)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

			$this->db->insert('groups', array('name' => 'admin'));
		}

		if (! $this->db->table_exists('user_groups')) {
			$this->db->query("CREATE TABLE user_groups (
				id int NOT NULL AUTO_INCREMENT,
				user_id int NOT NULL default '0',
				group_id int NOT NULL default '0',
				PRIMARY KEY  (id)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

			$this->db->insert('user_groups', array('id' => 1, 'user_id' => 1, 'group_id' => 1));
		}
		
		if (! $this->db->table_exists('group_contents')) {
			$this->db->query("CREATE TABLE group_contents (
				id int NOT NULL AUTO_INCREMENT,
				group_id int NOT NULL default '0',
				content_id int NOT NULL default '0',
				permission tinyint(4) NOT NULL default '0',
				PRIMARY KEY  (id)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

			$this->db->insert('group_contents' array('group_id' => 1, 'content_id' => 1, 'permission' => 15));
		}

		// Settings Table
		if (! $this->db->table_exists('settings')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS settings (
				id int NOT NULL AUTO_INCREMENT,
				user_id int NOT NULL DEFAULT 0,
				name varchar(255) NOT NULL,
				value text NOT NULL,
				customize enum('No', 'Yes') NOT NULL DEFAULT 'No',
				PRIMARY KEY (id)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

			$this->db->insert('settings', array('user_id' => 0, 'name' => 'company_name', 'value' => '', 'customize' => 'No'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'company_address', 'value' => '', 'customize' => 'No'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'company_contact', 'value' => '', 'customize' => 'No'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'company_email', 'value' => '', 'customize' => 'No'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'company_website', 'value' => '', 'customize' => 'No'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'company_pan', 'value' => '', 'customize' => 'No'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'company_bank', 'value' => '', 'customize' => 'No'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'rows_per_page', 'value' => '15', 'customize' => 'Yes'));
			$this->db->insert('settings', array('user_id' => 0, 'name' => 'printer_name', 'value' => 'LQ-1150', 'customize' => 'Yes'));
		}
	}

	public function down() {
		$this->dbforge->drop_table('ci_sessions');
		$this->dbforge->drop_table('captcha');

		$this->dbforge->drop_table('contents');
		$this->dbforge->drop_table('new_users');
		$this->dbforge->drop_table('users');
		$this->dbforge->drop_table('groups');
		$this->dbforge->drop_table('user_groups');
		$this->dbforge->drop_table('group_contents');
		
		$this->dbforge->drop_table('settings');
	}
}
