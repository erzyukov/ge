<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Ядро движка Global Engine.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class GE_Core {
	
	private static $version = '2.0.1';
	private static $gemod = array();
// TODO почистить переменную виджет
	private static $widget = array();
	private static $lang = array();
	private static $lang_uri = array();
	
	/**
	 *  Возвращает текущую версию движка
	 */
	public static function ver(){
		return self::$version;
	} 
	
	
	public static function initialize(){
		
		try {
			$lang  = ORM::factory('lang')->find_all()->as_array();
			foreach ($lang as $v) {
				self::$lang[$v->id] = $v->as_array();
				self::$lang_uri[$v->uri] = $v->as_array();
			}
			self::$gemod = ORM::factory('module')->get_list();
			self::$widget = ORM::factory('widget')->get_list();
		}
		catch (Exception $e) {
			return true;
		}
		
		// инициируем конфиг модулей
		// конфиг теперь работает черезб БД
		
	}

	/**
	 * Возвращает состаяние разработки сайта ('production')
	 */
	public static function production(){
		return Kohana::config('ge.production');
	}
	
	/**
	 * Возвращает модель указанного модуля сайта
	 * 
	 * @param string $module
	 * @param integer $id
	 * 
	 * @return Model_Gemod
	 */
	public static function mod($module, $id = NULL){

		Model_Gemod::$module_table = GE::pref('mod', $module);
		return new Model_Gemod($id);
		
	} 

	
	/**
	 * Создает и возвращает экземпляр объекта View
	 * если задан параметр $create = TRUE, то создается директория и шаблон, если их нет
	 * в папке APPPATH
	 * 
	 * @param unknown_type $view
	 * @param unknown_type $data
	 * @param unknown_type $create
	 */
	public static function view($view, $data, $create = FALSE){
		
		if ($create AND !Kohana::find_file('views', $view, 'tpl')){
			$file = APPPATH.'views/'.$view.'.tpl';
			self::test_file($file);
			self::include_view_vars($file, $data);		
		}
		
		return View::factory('smarty:'.$view, $data);
	}
	
	public static function fields($fields, $lang){
		return new Fields($fields, $lang);
	}


	/*
	 * 
	 * @return Request
	 */
	public static function request($url = NULL){
		if ($url)
			$request = Request::factory($url);
		else
			$request = Request::instance();

		if ($request->action == 'short'){
			$id = $request->param('id');
			$short_map = Sitemap::instance()->get_sitemap();
			if (isset($short_map[$id])){
				$lang = $request->param('lang', GE::lang('uri'));
				$module = $short_map[$id]['name'];
				$id = $short_map[$id]['item_id'];
				return Request::factory(GE::get_module_url($lang, $module, $id, FALSE));
			}
			else{
				return Request::factory('error/404');
			}
			
		}
		
		return $request;
	}
	
	
	public static function get_module_url($lang, $module, $id, $short = TRUE){
		$map = Sitemap::instance()->get_sitemap(( (int)$module ) ? 'module_id': 'module');
		
		if (isset($map[$module][$id])){
			$item = $map[$module][$id];
			if ($item['short_url'] != '' AND $short){
				$url = URL::base().Route::get('short')->uri(array(
					'id' => $item['short_url'],
					'action' => '',
					'lang' => ($lang == GE::lang('uri'))? NULL: $lang,
				));
			}
			else{
				$url = URL::base().Route::get('module')->uri(array(
					'module' => $item['name'],
					'id' => ($item['item_id'])? $item['item_id']: NULL,
					'action' => '',
					'lang' => ($lang == GE::lang('uri'))? NULL: $lang,
				));
			}
		}
		else{
			$url = '/error/404';
		}
		
		return $url;
	}
	
	
//	public static function get_module_url($module, $id, $short = TRUE){
//		$url = '';
//		$map = Gengine::get_sitemap(( is_int($module) ) ? 'module_id': 'module');
//	
//		if (isset($map[$module][$id])){
//			$item = $map[$module][$id];
//			if ($item['short_url'] != '' AND $short){
//				$url = URL::base().Route::get('short')->uri(array(
//					'id' => $item['short_url'],
//					'action' => '',
//				));
//			}
//			else{
//				$url = URL::base().Route::get('module')->uri(array(
//					'module' => $item['name'],
//					'id' => ($item['item_id'])? $item['item_id']: NULL,
//					'action' => '',
//				));
//			}
//		}
//		else{
//			$url = '/error/404';
//		}
//		
//		return $url;
//	}
	

	/**
	 * Проверяет файл на существование, 
	 * если его не существует - создается файл вместе с каталогами
	 * 
	 * @param string $test_path
	 */
	public static function test_file($test_path){
		if ( ! file_exists($test_path)){
			self::test_dir(dirname($test_path));
			$fp = fopen($test_path, 'w');
			fclose($fp);
		}
	}
	
	public static function include_view_vars($file, $data){
		$vars = self::get_array_keys($data);
		$vars = "\r\n"."\r\n".'{*'."\r\n".$vars.'*}';
		$f = fopen($file, 'wb');
		fwrite($f, $vars);
		fclose($f);
	}

	
	protected static function get_array_keys($arr, $current_key = ''){
		$result = '';
		$keys = array_keys($arr);
		$pref = ($current_key != '')? $current_key.'.': '';
		
		if (isset($keys[0])){
			if (is_int($keys[0]))
				return self::get_array_keys($arr[$keys[0]], $pref.'[array]')."\r\n";
//				return '  [array]';
		}
		else
			return '';
		
		foreach($keys as $v){
			$result .= '$'.$pref.$v."\r\n";
			
			if (is_array($arr[$v])){
				$result .= self::get_array_keys($arr[$v], $pref.$v)."\r\n";
			}
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @param string $test_path
	 */
	public static function test_dir($test_path){
		if (!is_dir($test_path))
			mkdir($test_path, 0755, TRUE);
	}
	
	
	
	/**
	 * Устанавливает роуты необходимые для работы движка
	 */
	public static function set_routes(){
		
		foreach (self::$gemod as $module){
			
			if ($module['controller']){
				self::set_module_custom_route($module['name'], $module['controller']);
			}
			
		}
		
	}
	
	/**
	 * Устанавливает роуты необходимые для работы движка
	 */
	public static function set_module_custom_route($module, $controller){

		Route::set($module, '(<lang>/)'.$module.'(/<id>)(/<action>)(/p<page>)(/*<template>)(/l<limit>)(/$<p1>(/$<p2>(/$<p3>(/$<p4>(/$<p5>)))))', array(
					'lang' => '(?:'.implode('|', GE::lang_list()).')',
					'id' => '\d+',
					'page' => '\d+',
					'action' => '[a-zA-Z]+',
					'template' => '[a-zA-Z]+',
					'limit' => '\d+',
				))->defaults(array(
				'lang' => GE::lang('uri'),
				'module' => $module,
				'controller' => $controller,
				'action'     => 'index',
			));
		
	}
	
	
	/**
	 * Возвращает язык по умолчанию, который прописывается в конфиге "lang"
	 * 
	 * @param $param
	 * 
	 * @return array
	 */
	public static function lang($param = ''){
		$search = 'lang.default'.($param?'.'.$param:'');
		
		if (Kohana::config($search)){
			return Kohana::config($search);
		}

		throw new Kohana_Exception('Can\'t found default lang in config!');
	}
	
	/**
	 * Возвращаяет либо запрошеный префикс, либо полное название таблицы с префиксом
	 *   если тип префикса "sys" (системные таблицы), то дополнительно проводится проверка на
	 *   наличие названия таблицы в конфиге
	 * 
	 * @param $type тип префикса
	 * @param $table название таблицы
	 * 
	 * @return string
	 */
	public static function pref($type = 'mod', $table = ''){
		$table = trim($table);
		$config_type = $type.'_prefix';
		if (Kohana::config('dbtable.'.$config_type) === NULL)
			throw new Kohana_Exception('Can\'t found prefix "'.$config_type.'" in config!');
		if ($type != 'sys' AND $table != '' AND Kohana::config('dbtable.'.$config_type) === NULL)
			throw new Kohana_Exception('Can\'t found table name "'.$config_type.'" in config!');
		$table = ($type == 'sys' AND $table != '')? Kohana::config('dbtable.'.$table): $table;
		return Kohana::config('dbtable.'.$config_type).$table;
	}
	

	/**
	 * Возвращает список модулей с их настройками
	 * 
	 * @param $parent_id родительский идентификатор
	 * @param $key_int признак типа ключей возвращаемого массива (TRUE - int, FALSE - string (module name))
	 * 
	 * @return array
	 */
	public static function modules($parent_id = 0, $key_int = TRUE ){
		$result = array();
		if ($parent_id){
			foreach (self::$gemod as $mod){
				if ($mod['parent_id'] == $parent_id){
					$result[$parent_id] = $mod;
					continue;
				}
			}
		}
		else{
			$result = self::$gemod;
		}
		
		if (!$key_int){
			$tmp = array();
			foreach($result as $item){
				$tmp[$item['name']] = $item;
			}
			$result = $tmp;
		}
		
		return $result;
	}
	
	/**
	 * Возвращает массив вида [module => type]
	 * либо тип модуля, если передано название модуля
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public static function module_type($name = ''){
		$result = array();
		foreach (self::$gemod as $v){
			$result[$v['name']] = $v['type'];
		}
		if ($name AND isset($result[$name]))
			return $result[$name];
		return $result;
	}
	
	/**
	 * Возвращает список модулей сайта в виде массива
	 * 
	 * @return array
	 */
	public static function module_list(){
		$result = array();
		foreach (self::$gemod as $i => $v){
			$result[$i] = $v['name'];
		}
		return $result;
	}

	/**
	 * Возвращает список виджетов сайта в полном виде
	 * 
	 * @return array
	 */
	public static function widgets(){
		return self::$widget;
	}
	
	/**
	 * Возвращает список виджетов сайта в виде списка названий
	 * 
	 * @return array
	 */
	public static function widget_list(){
		$result = array();
		foreach (self::$widget as $i => $v){
			$result[$i] = $v['name'];
		}
		return $result;
	}
	
	/**
	 * Возвращает список существующих возвращаемых типов данных для виджетов
	 * 
	 * @return array
	 */
	public static function widget_types(){
		return array_keys(Kohana::config('widget.types'));
	}
	
	
	public static function lang_list($id = NULL, $field = 'uri') {
		if ($id)
			return self::$lang[$id]['uri'];
		$result = array();
		foreach (self::$lang as $v){
			$result[$v['id']] = ($field)? $v[$field] : $v;
		}
		return $result;
	}
	
	public static function get_lang($uri = NULL) {
		if ($uri === NULL)
			return self::$lang;
		else if ((int)$uri)
			return self::$lang[$uri];
		else
			return self::$lang_uri[$uri];
	}
}
