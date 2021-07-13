<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'title' => 'Отслеживание груза',
	'description' => 'Отслеживание грузов',
	'resources' => array(
		array(
			'type' => 'js',
			'link' => 'cargotracker.js',
		),
	),
//	'cookie' => 'poll',
	'tables' => array(
		'cargo_tracker' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'user_id',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'number',
					'sql_length' => '50',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'date',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'action',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'receiver',
					'sql_length' => '100',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'from',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'to',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
			),
		),
		'cargo_destination' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'value',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
			),
		),
		'cargo_action' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'value',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
			),
		),
	),

);

