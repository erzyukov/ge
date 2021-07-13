<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Класс для генераторов контента.
	 * Описание функций интерфейса находятся непосредственно в описании интерфейса
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
	
	class GE_Admin_Content_Generator implements GE_Interface_Content_Generator {
		
		protected $model;
		protected $field_model;
		protected $param;
		
		protected $item_id = NULL;
		protected $lang = NULL;
		protected $template = NULL;
		
		protected $template_dir;

		public function __construct($module){
			// выставляем язык по умолчанию
			$this->lang = GE::get_lang(GE::lang('id'));
			$this->template_dir = 'admin/module/'.GE::module_type($module).'/';
			$this->param = ORM::factory('module')->get_module($module);
			$this->field_model = unserialize($this->param->model);
		}
		
		public function content() {}
		
		public function navigation() {}
		
		public function title() {}
		
		public function menu() {}
		
		public function keywords() {}
		
		public function description() {}
		
		public function js() {}
		
		public function css() {}
		
		
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
			if (Kohana::find_file('classes', 'admin/generator/'.$module_name, 'php')){
				$class = 'Admin_Generator_'.ucfirst($module_name);
				return new $class($module_name);
			}
			// иначе, если генератор найден в модуле GE запускаем его
			else if (Kohana::find_file('classes', 'ge/admin/content/generator/'.$type, 'php')) {
				$class = 'GE_Admin_Content_Generator_'.ucfirst($type);
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
		 * Возвращает системную информацию о записи модуля
		 * 
		 * @param int $module_id
		 * @param int $item_id
		 * @param int $lang_id
		 */
		protected function get_item_system_info($module_id, $item_id, $lang_id){
			
			$data = array();
			$map = Sitemap::instance()->get_sitemap('module_id');
			$item_map = $map[$module_id][$item_id];
			
			$translate = array(
				'title' => '',
				'keywords' => '',
				'description' => '',
			);
			
			if (isset($item_map['translate'][$lang_id]))
				$translate = $item_map['translate'][$lang_id];

			$data['title'] = $translate['title'];
			$data['keywords'] = $translate['keywords'];
			$data['description'] = $translate['description'];
			$data['short_url'] = $item_map['short_url'];
			$data['priority'] = $item_map['seo_priority'];
			$data['priority_list'] = $this->param->get_priority_options();
			$data['sitemap_show'] = $item_map['sitemap_show'];
			$data['changefreq'] = $item_map['seo_changefreq'];
			$data['changefreq_list'] = $this->param->get_changefreq_options();
			$data['lastmod'] = $item_map['lastmod'];
			$data['user'] = Manageauth::instance()->get_user()->name;
			
			return $data;
		}
		
		/**
		 * Создает массив для создания записи в карте сайта
		 * 
		 * @param int $item_id
		 * @param string $sitemap_title
		 * @param int $item_parent_id
		 * @param int $sitemap_show
		 */
		protected function get_sitemap_value($item_id, $sitemap_title = '', $item_parent_id = 0, $sitemap_show = 1){

			$result = array(
				'module_id' => $this->param->id,
				'item_id' => $item_id,
				'item_parent_id' => $item_parent_id,
				'sitemap_show' => $sitemap_show,
				'lastmod' => date('Y-m-d H:i:s'),
				'seo_priority' => $this->param->seo_priority,
				'seo_changefreq' => $this->param->seo_changefreq,
			);

			if ($sitemap_title){
				$short_url = Utils::translit_url($sitemap_title);
				$result['short_url'] = $short_url;
			}
			
			return $result;
		}
		
		/**
		 * Создаем массив для создания записей перевода записи катры сайта
		 * 
		 * @param int $lang_id
		 * @param string $sitemap_title
		 */
		protected function get_sitemap_translate_values($lang_id, $sitemap_title){
			$result = array();
			
			$result[] = array(
				'lang_id' => $lang_id,
				'title' => $sitemap_title,
				'caption' => $sitemap_title,
			);
			if ($lang_id != GE::lang('id')){
				$result[] = array(
					'lang_id' => GE::lang('id'),
					'title' => $sitemap_title,
					'caption' => $sitemap_title,
				);
			}
			
			return $result;
		}
		
		
		
		/**
		 * Возвращает массив ссылок для элемента списка, в зависимости от типа модуля
		 * 
		 * @param int $id
		 */
		protected function get_item_href($id){
			$result = array();
			
			$result['_edit_href'] = Manage::get_module_url($this->param->name, 'edit', $id);

/*  >> ==================================================================  */
/*  4sub  */
			$sub = GE::modules($this->param->id);
			if (count($sub)){
				$result['_sub_href'] = Manage::get_module_url($sub[$this->param->id]['name'], 'parent', $id);
			}
/*  << ==================================================================  */
			
			// TODO \\//
			// так же здесь для дерева необходимо генерировать собственных подразделов
			
			return $result;
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
		

	}



