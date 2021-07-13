<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Карта сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class GE_Sitemap {

	protected static $instance = NULL;
	protected static $sitemap = array();
	
	private function __construct(){
		
	}
	
	private function __clone() {}
	
	
	public static function instance(){
		if (self::$instance === NULL){
			self::$instance = new self();
		}
		return self::$instance;
	}
	


	/**
	 * Возвращает всю карту сайта в виде массива
	 * формат массива задается с помощью параметра
	 * 
	 * short     - ключ массива короткий урл
	 * module    - ключ массива название модуля
	 * module_id - ключ массива идентификатор модуля
	 * tree      - ключ массива идентификатор модуля в виде дерева по ключу parent_id
	 * 
	 * @param string $type
	 * @return array
	 */
	public function get_sitemap($type = 'short'){
		
		if (isset(self::$sitemap[$type])){
			return self::$sitemap[$type];
		}
		else{
// TODO здесь надо будет оформить кэширование
/*			if (Kohana::config('config.mapcache')){
				// пробуем достать из файлового кэша
				Gengine::test_file(APPPATH.Config::CACHEPATH.'map.txt');
				$map_cache = file_get_contents(APPPATH.Config::CACHEPATH.'map.txt');
				if (strlen($map_cache)){
					$map_cache = unserialize($map_cache);
					if (isset($map_cache[$type])){
						self::$sitemap = $map_cache;
						return $map_cache[$type];
					}
				}
			}
*/			
			$short = array();
			$module = array();
			$tree = array();
			$module_id = array();
			
			foreach (ORM::factory('sitemap')->find_all() as $map){

				$translate = array();
				foreach ($map->translates->find_all() as $tr){
					$translate[$tr->lang_id] = $tr->as_array();
				}
				$name = $map->module->name;
				$array = array_merge($map->as_array(), array('name' => $name, 'translate' => $translate));
				
				if ($map->short_url != '')
					$short[$map->short_url] = $array;
				$module[$name][$map->item_id] = $array;
				$tree[$name][$map->item_parent_id][$map->item_id] = $array;
				$module_id[$map->module_id][$map->item_id] = $array;
			}

			self::$sitemap['short'] = $short;
			self::$sitemap['module'] = $module;
			self::$sitemap['tree'] = $tree;
			self::$sitemap['module_id'] = $module_id;

//			if (Kohana::config('config.mapcache'))
//				file_put_contents(APPPATH.Config::CACHEPATH.'map.txt', serialize(self::$sitemap));
			
			if (isset(self::$sitemap[$type])){
				return self::$sitemap[$type];
			}
			else{
				throw new Kohana_Exception('Unknown type of sitemap!');
			}
		}
	}
	
	/**
	 * Обновляет время модификации у записи по заданным параметрам
	 * 
	 * @param int $module_id
	 * @param int $item_id
	 */
	public function touch($module_id, $item_id = 0){
		$map = ORM::factory('sitemap');
		$map->where('module_id', '=', $module_id)->where('item_id', '=', $item_id)->find();
		$map->values(array('lastmod' => date('Y-m-d h:i:s')));
		$map->save();
	}
	
	/**
	 * Устанавливает значение активности у записи по заданным параметрам
	 * 
	 * @param int $module_id
	 * @param int $item_id
	 */
	public function active($module_id, $item_id = 0, $active = 1){
		$map = ORM::factory('sitemap');
		$map->where('module_id', '=', $module_id)->where('item_id', '=', $item_id)->find();
		$map->values(array('isactive' => $active));
		$map->save();
	}
	
	/**
	 * Создает запись карты сайта
	 * если указаны заголовки для перевода - создаются заголовки
	 * 
	 * @param array $sitemap
	 * @param array $translates - array(lang_id => array())
	 */
	public function create_record($sitemap, $translates = NULL){

		//$module_id, $item_id, $short_url = '', $item_parent_id = 0
		//isactive, sitemap_show, lastmod, seo_priority, seo_changefreq
		$map = ORM::factory('sitemap');
		$map->values($sitemap);
		$map->save();
		
		if ($translates)
			$this->create_translate_record($map->id, $translates);
		
	}
	
	/**
	 * Создает переводы заголовков для записи карты сайта
	 * 
	 * @param array $translates - array(lang_id => array())
	 */
	public function create_translate_record($sitemap_id, $translates){

		// sitemap_id, lang_id, title, keywords, description
		$trmap = ORM::factory('trsitemap');
		foreach ($translates as $translate){
			$translate['sitemap_id'] = $sitemap_id;
			$trmap->values($translate);
			$trmap->save();
			$trmap->clear();
		}
	}
	
	public function update_record($module_id, $item_id, $sitemap, $translates = NULL){
		$map = ORM::factory('sitemap')
			->where('module_id', '=', $module_id)->where('item_id', '=', $item_id)->find();
		$map->values($sitemap);
		$map->save();
		
		if ($translates)
			$this->update_translate_record($map->id, $translates);
	}
	
	public function update_translate_record($sitemap_id, $translates){
		$trmap = ORM::factory('trsitemap');
		foreach ($translates as $translate){
			$lang_id = ($translate['lang_id'])? $translate['lang_id']: GE::lang('id');
			$trmap->where('sitemap_id', '=', $sitemap_id)->where('lang_id', '=', $lang_id)->find();
			if (!$trmap->id)
				$translate['sitemap_id'] = $sitemap_id;
			$trmap->values($translate);
			$trmap->save();
			$trmap->clear();
		}
	}
	
	public function delete_record($module_id, $item_id){
		$map = ORM::factory('sitemap')
			->where('module_id', '=', $module_id)->where('item_id', '=', $item_id)->find();
		$map->delete();
	}

	public function delete_translate_record($sitemap_id){
		$trmap = ORM::factory('trsitemap');
		$trmap->where('sitemap_id', '=', $sitemap_id);
		$trmap->delete_all();
	}
	
}