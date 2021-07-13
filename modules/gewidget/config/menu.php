<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'title' => 'Меню сайта',
	'description' => 'Для создания различных меню сайта',
	'tables' => array(
		'menu' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'code',
					'sql_length' => '20',
					'sql_type' => 'varchar',
					'unique' => 1,
				), 
				array(
					'name' => 'title',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				),
			) 
		),
		'menu_element' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'parent_id',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'title',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'menu_id',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'module_id',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'item_id',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'direct_link',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'outorder',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				
			), 
		)
	),
	'links' => array(
		array(
			'parent_table' => 'menu', 
			'child_table' => 'menu_element', 
			'parent_key' => 'id', 
			'child_key' => 'menu_id', 
			'ondelete' => 'CASCADE', 
			'onupdate' => 'RESTRICT',
		),
	),

);

