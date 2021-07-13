<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'hash_method' => 'sha1',
	'salt_pattern' => '1, 3, 5, 9, 14, 15, 20, 21, 28, 30',
	'lifetime' => 3600,
	'log_try' => 5,
	'session_key' => 'wuser',
	'title' => 'Пользователи',
	'description' => 'Пользователи сайта',
	'day' => array(
		1 => 'Пн',
		2 => 'Вт',
		3 => 'Ср',
		4 => 'Чт',
		5 => 'Пн',
		6 => 'Сб',
	),
	'data' => array(
		'fio' => array(
			'title' => 'ФИО',
			'type' => 'text',
			'obl' => 1,
		),
	),
	'tables' => array(
		'user' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'login',//login
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'mail',//login
					'sql_length' => '30',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'data',
					'sql_length' => NULL,
					'sql_type' => 'text',
				),
				array(
					'name' => 'last_login',
					'sql_length' => '11',
					'sql_type' => 'int',
				),
				array(
					'name' => 'password',
					'sql_length' => '50',
					'sql_type' => 'varchar',
				),
				array(
					'name' => 'isactive',
					'sql_length' => '1',
					'sql_type' => 'int',
				),
				array(
					'name' => 'cookie',
					'sql_length' => '50',
					'sql_type' => 'varchar',
				),
				array(
					'name' => 'restore',
					'sql_length' => '32',
					'sql_type' => 'varchar',
				),
			),
		),
	),

);
