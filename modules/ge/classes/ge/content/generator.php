<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Базовый класс для генераторов контента.
	 * Описание функций интерфейса находятся непосредственно в описании интерфейса
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
	
	class GE_Content_Generator implements GE_Interface_Content_Generator {
		
		protected $model;
		protected $param;
		protected $module;
		
		protected $item_id = NULL;
		protected $page = NULL;
		protected $limit = NULL;
		protected $lang = NULL;

//		protected $navigation = array();
		protected $mode = NULL;
		protected $current_template = 'index';
		
		public function __construct($module){
			$this->module = $module;
			// выставляем язык по умолчанию
			$this->lang = GE::get_lang(GE::lang('id'));
			$this->param = ORM::factory('module')->get_module($module);
		}
		
		public function content() {}
		
		public function navigation() {}
		
		public function title() {}
		
		public function menu() {}
		
		public function keywords() {}
		
		public function description() {}
		
		public function js() {
			$js = ORM::factory('customjs');
			return $js->get_item($this->param->id, $this->item_id);
		}
		
		public function css() {
			$css = ORM::factory('customcss');
			return $css->get_item($this->param->id, $this->item_id);
		}
		
		
		/**
		 * Создает и возвращает объект генератор контента
		 * в зависимости от типа модуля
		 * тип модуля определяется по названию модуля
		 * 
		 * @param $module_name
		 * @return Generator
		 */
		public static function factory($module_name) {

			$type = GE::module_type($module_name);
			// если генератор найден в папке application запускаем его
			if (Kohana::find_file('classes', 'generator/'.$module_name, 'php')){
				$class = 'Generator_'.ucfirst($module_name);
				return new $class($module_name);
			}
			// иначе, если генератор найден в модуле GE запускаем его
			else if (Kohana::find_file('classes', 'ge/content/generator/'.$type, 'php')) {
				$class = 'GE_Content_Generator_'.ucfirst($type);
				return new $class($module_name);
			}
			// иначе, даем знать что нет генератора
			else {
				throw new Kohana_Exception('Site module "'.$module_name.'" (type: '.$type.') not found!');
			}
		}
		
		/**
		 * Устанавливает язык страницы
		 * @param string $lang
		 */
		public function set_lang($lang){
			$this->lang = GE::get_lang($lang);
		}
		
		/**
		 * Устанавливает идентификатор страницы
		 * @param string $id
		 */
		public function set_id($id){
			$this->item_id = $id;
		}
		
		/**
		 * Устанавливает номер страницы, при разбиении списка по страницам
		 * @param string $page
		 */
		public function set_page($page){
			$this->page = $page;
		}
		
		/**
		 * Устанавливает количество элементов списка на странице
		 * @param string $limit
		 */
		public function set_limit($limit){
			$this->limit = $limit;
		}
		
		/**
		 * Возвращает шаблон который определил генератор
		 * 
		 * @return sting
		 */
		public function get_template(){
			return $this->current_template;
		}
		
		/**
		 * Проверяет есть ли подмодуль у текущего модуля
		 *  если есть возвращает его
		 *  если нет возвращает FASLE
		 */
		protected function get_child(){
			$sub = GE::modules($this->param->id);
			if (count($sub))
				return $sub[$this->param->id];
			return FALSE;
		}
		
		/**
		 * Возвращает массив навигации до заданного модуля (simple) и элемента
		 * 
		 * @param int $module_id идентификатор модуля
		 * @param int $item_id идентификатор элемента
		 * @param bool $is_root показатель, того что модуль является коренным и у него нет родителей
		 */
		protected function get_simple_navigation($module_id, $item_id, $is_root = TRUE){
			$result = array();
			$map = Sitemap::instance()->get_sitemap('module_id');
			if (isset($map[$module_id][$item_id])){
				$item_parent_id = $map[$module_id][$item_id]['item_parent_id'];
				if ($item_parent_id != -1 && $is_root){
					$result[] = $this->generate_navigation_item($map, $module_id, $item_parent_id);
				}
				$result[] = $this->generate_navigation_item($map, $module_id, $item_id);
			}
			return $result;
		}
		
		/**
		 * Возвращает массив навигации до заданного модуля (tree) и элемента
		 * 
		 * @param int $module_id идентификатор модуля
		 * @param int $item_id идентификатор элемента
		 */
		protected function get_tree_navigation($module_id, $item_id){
			$map = Sitemap::instance()->get_sitemap('module_id');
			return $this->_get_tree_navigation($module_id, $item_id, $map);
		}
		
		/**
		 * Рекурсивная функция добычи массива навигации
		 * 
		 * @param int $module_id
		 * @param int $item_id
		 * @param int $map
		 */
		private function _get_tree_navigation($module_id, $item_id, $map){
			$result = array();
			if (isset($map[$module_id][$item_id])){

				$result[] = $this->generate_navigation_item($map, $module_id, $item_id);
				
				$pid = $map[$module_id][$item_id]['item_parent_id'];
				if ($pid != -1){
					$result = array_merge($this->_get_tree_navigation($module_id, $pid, $map), $result); 
				}
				
			}
			return $result;
		}
		
		/**
		 * Генерирует элемент навигации
		 * 
		 * @param $map
		 * @param int $module_id
		 * @param int $item_id
		 */
		private function generate_navigation_item($map, $module_id, $item_id){
			$map_tr = $map[$module_id][$item_id]['translate'];
			$caption = isset($map_tr[$this->lang['id']])? $map_tr[$this->lang['id']]['title'] : $map_tr[GE::lang('id')]['title'];
			return array('title' => $caption, 'url' => GE::get_module_url($this->lang['uri'], $module_id, $item_id));
		}
		
		protected function get_module_parent_navigation($module_id, $item_id){
			$result = array();
			
			$pid = ($item_id)? $item_id: 0;
			$modules = GE::modules();
			$ptype = $modules[$module_id]['type'];
				
			if ($ptype == 'simple')
				$result = $this->get_simple_navigation($module_id, $pid);
			else if ($ptype == 'tree')
				$result = $this->get_tree_navigation($module_id, $pid);

			return $result;
		} 
		
	}



