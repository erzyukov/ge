<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Admin_Group extends ORM {

	protected $_table_name = 'gsys_manage_group';
	
	// Relationships
	protected $_has_many = array
		(
			'users'		=> array('model' => 'admin_user', 'foreign_key' => 'group_id'),
			'access'	=> array('model' => 'admin_access', 'foreign_key' => 'group_id'),
		);
	
	// Rules
	protected $_rules = array
	(
		'name'			=> array
		(
			'not_empty'		=> NULL,
			'min_length'		=> array(4),
			'max_length'		=> array(50),
		),
	);
		
	
	
	
	
	
	
	
	
	
	
} // End Model_Admin_Group