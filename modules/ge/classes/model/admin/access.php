<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Admin_Access extends ORM {

	protected $_table_name = 'gsys_manage_access';
	protected $_sorting = array('group_id' => 'ASC');
	protected $_primary_key = 'name';
	
	// Relationships
	protected $_belongs_to = array
		(
			'group' => array('model' => 'admin_group', 'foreign_key' => 'group_id')
		);
	
		
	
	
	
	
	
	
	
	
} // End Model_Admin_Access