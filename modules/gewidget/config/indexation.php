<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'title' => 'Индексация сайта',
	'description' => 'Индексация сайта для поиска по сайту',
	'exept_link' => array(
		'/basket',
		'/search',
	),
	'tags_weight' => array(
		'b' => 2,
		'u' => 2,
		'strong' => 2,
		'h1' => 3,
		'h2' => 3,
		'h3' => 3,
		'h4' => 3,
	),
	'content_class' => 'center',
	'tables' => array(
		'search_url' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '10',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'name',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'title',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
			),
		),
		'search_keyword' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '10',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'name',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
			), 
		),
		'search_url_key' => array(
			'fields' => array(
				array(
					'name' => 'url_id',
					'sql_length' => '10',
					'sql_type' => 'int',
					'primary' => 1,
				), 
				array(
					'name' => 'key_id',
					'sql_length' => '10',
					'sql_type' => 'int',
					'primary' => 1,
				), 
				array(
					'name' => 'weight',
					'sql_length' => '10',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'cont',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
			), 
		),
	),

);

