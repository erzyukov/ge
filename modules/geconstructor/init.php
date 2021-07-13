<?php defined('SYSPATH') or die('No direct script access.');

// 
Route::set('constructor', 'constructor(/<action>)(/id<id>)(/<paction>)', array(
		'id' => '\d+',
	))
	->defaults(array(
		'controller' => 'constructor',
		'action'     => 'index',
	));
	
