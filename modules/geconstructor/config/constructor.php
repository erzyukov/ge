<?php defined('SYSPATH') or die('No direct script access.');

return array(

	'module_type' => array(
		'simple' => array('title' => 'Список', 'table' => 1),
		'tree' => array('title' => 'Дерево', 'table' => 1),
		'custom' => array('title' => 'Без структуры', 'table' => 0),
	),
	
	'reference_type' => array(
		'select' => array('title' => 'Селект'),
		'selecttree' => array('title' => 'Селект-дерево'),
		'multi' => array('title' => 'Мультиселект'),
		'checkbox' => array('title' => 'Чекбоксы'),
	),
	
	'select_type' => array(
		'select' => array('title' => 'Селект'),
		'multi' => array('title' => 'Мультиселект'),
		'checkbox' => array('title' => 'Чекбоксы'),
	),
	
	'field_type' => array(
		'string' => array(
			'title' => 'Строка', 
			'sql' => 'INT(11)', 
		),
		'text' => array(
			'title' => 'Текст', 
			'sql' => 'INT(11)', 
		),
		'editor' => array(
			'title' => 'Редактор', 
			'sql' => 'INT(11)', 
		),
		'number' => array(
			'title' => 'Число', 
			'sql' => 'INT(11)', 
		),
		'cost' => array(
			'title' => 'Цена', 
			'sql' => 'FLOAT', 
		),
		'image' => array(
			'title' => 'Картинка', 
			'sql' => 'INT(11)', 
		),
		'file' => array(
			'title' => 'Файл', 
			'sql' => 'VARCHAR(50)', 
		),
		'reference' => array(
			'title' => 'Ссылка', 
			'sql' => 'INT(11)', 
		),
		'select' => array(
			'title' => 'Статичный селект', 
			'sql' => 'VARCHAR(30)', 
			'type' => array('select', 'multi', 'selecttree', 'checkbox'),
		),
		'date' => array(
			'title' => 'Дата', 
			'sql' => 'DATE', 
		),
		'datetime' => array(
			'title' => 'Дата и время', 
			'sql' => 'DATETIME', 
		),
		'time' => array(
			'title' => 'Время', 
			'sql' => 'TIME', 
		),
		'hidden' => array(
			'title' => 'Скрытое поле', 
			'sql' => 'VARCHAR(255)', 
		),
		'checkbox' => array(
			'title' => 'Чекбокс', 
			'sql' => 'INT(1) DEFAULT "0"', 
		),
	),
	


);
