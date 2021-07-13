<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'title' => 'Голосование',
	'description' => 'Виджет голосования на сайте',
	'resources' => array(
		array(
			'type' => 'js',
			'link' => 'poll.js',
		),
	),
	'gc' => 10,
	'ip_life' => 3600, // 1 час
	'cookie_life' => 604800, // неделя
	'cookie' => 'poll',
	'tables' => array(
		'poll_question' => array(
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
					'name' => 'isactive',
					'sql_length' => '1',
					'sql_type' => 'int',
				),
			),
		),
		'poll_answer' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'question_id',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'caption',
					'sql_length' => '255',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'result',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
			), 
		),
		'poll_defender' => array(
			'fields' => array(
				array(
					'name' => 'id',
					'sql_length' => '11',
					'sql_type' => 'int',
					'primary' => 1,
					'autoinc' => 1,
				), 
				array(
					'name' => 'question_id',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
				array(
					'name' => 'ip',
					'sql_length' => '20',
					'sql_type' => 'varchar',
				), 
				array(
					'name' => 'time',
					'sql_length' => '11',
					'sql_type' => 'int',
				), 
			), 
		),
	),
	'links' => array(
		array(
			'parent_table' => 'poll_question', 
			'child_table' => 'poll_answer', 
			'parent_key' => 'id', 
			'child_key' => 'question_id', 
			'ondelete' => 'CASCADE', 
			'onupdate' => 'RESTRICT',
		),
		array(
			'parent_table' => 'poll_question', 
			'child_table' => 'poll_defender', 
			'parent_key' => 'id', 
			'child_key' => 'question_id', 
			'ondelete' => 'CASCADE', 
			'onupdate' => 'RESTRICT',
		),
	),

);

