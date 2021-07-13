<?php defined('SYSPATH') or die('No direct script access.');

	function _d(){
		if (func_num_args() === 0)
			return;
		$variables = func_get_args();
		foreach ($variables as $var)
		{
			echo Kohana::debug($var);
		}
	}

	GE::initialize();

	Route::set('default', '(<lang>(/))', array(
				'lang' => '(?:'.implode('|', GE::lang_list()).')',
			))->defaults(array(
			'lang' => GE::lang('uri'),
			'controller' => 'welcome',
			'action'     => 'index',
	));
	
	// Роут для отображения версии
	Route::set('ge.version', 'ge(.<action>)')
		->defaults(array(
			'controller' => 'ge',
			'action'     => 'version',
		));

		
// =====================================================================================	
// =====================================================================================	
		

	// Роут для админки логин
	Route::set('admin_account', 'admin/<action>', array(
	    		'action' => '(?:login|logout)',
			))->defaults(array(
			'directory'  => 'admin',
			'controller' => 'account',
			'action'     => 'login',
		));

	// Роут для админки виджетов
	Route::set('admin_widget', 'admin/widget(/<controller>(/<id>)(/<action>)(/$<p1>(/$<p2>(/$<p3>(/$<p4>(/$<p5>))))))', array(
	    		'controller' => '(?:'.implode('|', GE::widget_list()).')',
				'id' => '\d+',
				'action' => '[a-zA-Z]+',
			))->defaults(array(
			'directory'  => 'admin/widget',
			'controller' => 'default',
			'action'     => 'index',
		));
		
	// Роут для админки модулей
	Route::set('admin_module', 'admin/module/(<module>(/<action>(/<id>)))', array(
	    		'module' => '(?:'.implode('|', GE::module_list()).')',
				'id' => '\d+',
			))->defaults(array(
			'directory'  => 'admin',
			'controller' => 'module',
			'action'     => 'index',
			'id' => 0,
		));
	
	// Роут для настроек
	Route::set('admin_settings', 'admin/settings/(/<action>(/<id>))', array(
				'id' => '\d+',
			))->defaults(array(
			'directory'  => 'admin',
			'controller' => 'settings',
			'action'     => 'index',
			'id' => 0,
		));
		
		
	// Роут для админки (+ главная страница)
	Route::set('admin_default', 'admin(/<controller>(/<action>(/<id>)))')
		->defaults(array(
			'directory'  => 'admin',
			'controller' => 'default',
			'action'     => 'index',
		));
		
// =====================================================================================	
// =====================================================================================	
	
	// Роут для виджетов сайта
//	Route::set('widget', '(<lang>/)w<controller>(/<id>)(/<action>)(/$<p1>(/$<p2>(/$<p3>(/$<p4>(/$<p5>)))))', array(
//	    		'controller' => '(?:'.implode('|', GE::widget_list()).')',
//				'lang' => '(?:'.implode('|', GE::lang_list()).')',
//				'id' => '\d+',
//				'action' => '[a-zA-Z]+',
//			))->defaults(array(
//			'directory' => 'widget',
//			'lang' => GE::lang('uri'),
//			'controller' => 'widget',
//			'action'     => 'index',
//		));

	Route::set('widget', '(<lang>/)w<widget>(/<type>)(/<action>)(/$<p1>(/$<p2>(/$<p3>(/$<p4>(/$<p5>)))))', array(
	    		'widget' => '(?:'.implode('|', GE::widget_list()).')',
				'lang' => '(?:'.implode('|', GE::lang_list()).')',
				'action' => '[a-zA-Z]+',
				'type' => '(?:'.implode('|', GE::widget_types()).')',
			))->defaults(array(
			'lang'		=> GE::lang('uri'),
			'controller'=> 'widget',
			'action'	=> 'index',
			'type'		=> 'tpl',
		));

	// Роуты для кастомных контроллеров
	GE::set_routes();
	
	// Роут для получения содержимого модуля
	Route::set('module_data', 'mdata(/<type>)(/<action>)/data(/<path>)', array(
			'path' => '.+',
			'type' => '(?:'.implode('|', GE::widget_types()).')',
		))->defaults(array(
			'path'       => '',
			'controller' => 'mdata',
			'action'     => 'content',
			'type'		=> 'tpl',
		));

	// Роут для стандартных модулей сайта
	Route::set('module', '(<lang>/)<module>(/<id>)(/<action>)(/p<page>)(/*<template>)(/l<limit>)(/$<p1>(/$<p2>(/$<p3>(/$<p4>(/$<p5>)))))', array(
	    		'module' => '(?:'.implode('|', GE::module_list()).')',
				'lang' => '(?:'.implode('|', GE::lang_list()).')',
				'id' => '\d+',
				'page' => '\d+',
				'action' => '[a-zA-Z]+',
				'template' => '[a-zA-Z]+',
				'limit' => '\d+',
			))->defaults(array(
			'lang' => GE::lang('uri'),
			'controller' => 'module',
			'action'     => 'index',
		));
	
	// Роут для коротких урл
	// до контроллера доходить не должно, по пути должно перекидываться на полный путь
	Route::set('short', '(<lang>/)<id>', array(
			'lang' => '(?:'.implode('|', GE::lang_list()).')',
			))->defaults(array(
				'lang' => GE::lang('uri'),
				'controller' => 'module',
				'action'     => 'short',
		));


	
// =====================================================================================	
// =====================================================================================	
	
	
	// Роут для поимки всего остального
	Route::set('catch_all', '<path>', array('path' => '.+'))
		->defaults(array(
			'controller' => 'error',
			'action' => '404'
		));
	


/*
// Роут для админки логин
Route::set('admin_account', 'admin/<action>', array(
    		'action' => '(?:login|logout)',
		))->defaults(array(
		'directory'  => 'admin',
		'controller' => 'account',
		'action'     => 'login',
	));
	
// Роут для админки модулей
Route::set('admin_module', 'admin/module/(<module>(/<action>(/<id>)))', array(
    		'module' => '(?:'.implode('|', Gengine::module_list()).')',
			'id' => '\d+',
		))->defaults(array(
		'directory'  => 'admin',
		'controller' => 'module',
		'action'     => 'index',
		'id' => 0,
	));

// Роут для настроек
Route::set('admin_settings', 'admin/settings/(/<action>(/<id>))', array(
			'id' => '\d+',
		))->defaults(array(
		'directory'  => 'admin',
		'controller' => 'settings',
		'action'     => 'index',
		'id' => 0,
	));
		
// Роут для админки (+ главная страница)
Route::set('admin_default', 'admin(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'directory'  => 'admin',
		'controller' => 'default',
		'action'     => 'index',
	));
	*/
// =====================================================================================	
// =====================================================================================	

/*// 
Route::set('content', 'content(/<action>)/module(/<path>)', array('path' => '.+'))
	->defaults(array(
		'path'       => '',
		'controller' => 'mcontent',
		'action'     => 'content',
	));
	
// Автоматическая генерация роутов по настройкам сайта
//Gengine::set_route();
	

// Роут для стандартных модулей
Route::set('module', '<module>(/<id>)(/<action>)(/p<page>)(/*<template>)(/s<limit>)(/$<p1>(/$<p2>(/$<p3>(/$<p4>(/$<p5>)))))', array(
//    		'module' => '(?:'.implode('|', Gengine::module_list()).')',
    		'module' => '(?:m1|m2|m3)',
			'id' => '\d+',
			'page' => '\d+',
			'action' => '[a-zA-Z]+',
			'limit' => '\d+',
		))->defaults(array(
		'controller' => 'module',
		'action'     => 'index',
	));

// Роут для коротких урл
Route::set('short', '<id>')
	->defaults(array(
		'controller' => 'module',
		'action'     => 'short',
	));*/
