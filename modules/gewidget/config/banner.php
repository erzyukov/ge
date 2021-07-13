<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'title' => 'Баннерная система',
	'description' => 'Баннерная система',
	'path' => '/rs/widget/banner/',

	'type' => array(
		1 => 'jpg',
		2 => 'gif',
		3 => 'flash',
	),

//	'resources' => array(
//		array(
//			'type' => 'js',
//			'link' => 'banner.js',
//		),
//	),
	
//	'gc' => 10,
//	'ip_life' => 3600, // 1 час
//	'cookie_life' => 604800, // неделя
//	'cookie' => 'poll',
	
	'tables' => array(
		'banner_list' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'caption',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'position',
					'sql_length' => '3',
					'sql_type' => 'int',
				),
				array(
					'name' => 'href',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				),
				array(
					'name' => 'width',
					'sql_length' => '4',
					'sql_type' => 'int',
				),
				array(
					'name' => 'height',
					'sql_length' => '4',
					'sql_type' => 'int',
				),
				array(
					'name' => 'type',
					'sql_length' => '1',
					'sql_type' => 'int',
				),
				array(
					'name' => 'path',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				),
				array(
					'name' => '_show',
					'sql_length' => '11',
					'sql_type' => 'int',
				),
				array(
					'name' => '_click',
					'sql_length' => '11',
					'sql_type' => 'int',
				),
				array(
					'name' => 'max_show',
					'sql_length' => '11',
					'sql_type' => 'int',
				),
				array(
					'name' => 'max_click',
					'sql_length' => '11',
					'sql_type' => 'int',
				),
				array(
					'name' => 'max_date',
					'sql_length' => '11',
					'sql_type' => 'int',
				),
				array(
					'name' => 'isactive',
					'sql_length' => '1',
					'sql_type' => 'int',
				),
			),
		),
//		'banner_target' => array(
//			'fields' => array(
//				array(
//					'name' => 'id',
//					'sql_length' => '11',
//					'sql_type' => 'int',
//					'primary' => 1,
//					'autoinc' => 1,
//				), 
//				array(
//					'name' => 'banner_id',
//					'sql_length' => '11',
//					'sql_type' => 'int',
//				), 
//				array(
//					'name' => 'sitemap_id',
//					'sql_length' => '11',
//					'sql_type' => 'int',
//				), 
//			), 
//		),
	),
	'links' => array(
//		array(
//			'parent_table' => 'banner', 
//			'child_table' => 'banner_target', 
//			'parent_key' => 'id', 
//			'child_key' => 'banner_id', 
//			'ondelete' => 'CASCADE', 
//			'onupdate' => 'RESTRICT',
//		),
//		array(
//			'parent_table' => 'sitemap', 
//			'parent_table_pref' => 'sys', 
//			'child_table' => 'banner_target', 
//			'parent_key' => 'id', 
//			'child_key' => 'sitemap_id', 
//			'ondelete' => 'CASCADE', 
//			'onupdate' => 'RESTRICT',
//		),
	),
	
);

