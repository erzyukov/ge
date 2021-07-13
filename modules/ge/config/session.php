<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'database' => array(
		'group' => 'default',
		'table' => 'gsys_session',
		'lifetime' => 3600,
		'gc' => 5,
		'columns' => array('last_active' => 'last_active'),
	),
);
