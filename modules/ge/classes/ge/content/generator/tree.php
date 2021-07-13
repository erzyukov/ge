<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Класс генератор контента.
	 * Для модулей сайта типа "Список"
	 * Описание функций интерфейса находятся непосредственно в описании интерфейса
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
	
	class GE_Content_Generator_Tree	extends GE_Content_Generator
	{
		public function __construct($module){
			parent::__construct($module);
			$this->model = GE::mod($module);
		}
		
		public function content($action = '') {
			$data = array();
			if ($action == 'parent'){
				$this->mode = 'children';
				$data['list'] = $this->get_list_data(0, $this->item_id);
			}
			else if ($this->item_id) {
				$this->mode = 'item';
				$data['item'] = $this->get_item_data($this->item_id);
				$data['list'] = $this->get_list_data($this->item_id);
			}
			else {
				$this->mode = 'list';
				$data['list'] = $this->get_list_data(0);
			}
			$this->current_template = $this->mode;
			
			return $data;
		}
		
		/**
		 * Получает данные записи по идентификатору
		 * 
		 * @param int $id
		 */
		protected function get_item_data($id){
			$item = $this->model->get_item($id);
			$out = $this->prerare_item($item);
			$child = $this->get_child();
			if ($child){
				$g = Generator::factory($child['name']);
				$g->set_id($id);
				$g->set_lang($this->lang['uri']);
				$content = $g->content('parent');
				$out = array_merge($out, array('child' => $content));
			}
			return $out;
		}
		
		/**
		 * Обрабатывает данные записи
		 * 
		 * @param $item
		 */
		protected function prerare_item($item){
			
			if ($model = unserialize($this->param->model)){
				$fields = GE::fields($model, $this->lang['id']);
				$out = $fields->process($item);
				return $out;
			}
			return $item;
		}
		
		/**
		 * Получает данные активных записей раздела
		 */
		protected function get_list_data($module_id = NULL, $module_parent_id = NULL){
			$result = array();
			$list = $this->model->get_items(TRUE, $this->param->outorder, $module_id, $module_parent_id, $this->limit);
			foreach ($list as $item){
				$out = $this->prerare_tree_item($item);
				$out['href'] = GE::get_module_url($this->lang['uri'], $this->param->id, $item['id']);
				$result[] = $out;
			}
			return $result;
		}
		/**
		 * Обрабатывает данные записи элемента дерева
		 * 
		 * @param $item
		 */
		protected function prerare_tree_item($item){
			if ($model = unserialize($this->param->model)){
				$fields = GE::fields($model, $this->lang['id']);
				$out = $fields->process($item);
				return $out;
			}
			return $item;
		}
		
		public function navigation(){
			$result = array();

			// текущий элемент
			$id = ($this->item_id)? $this->item_id : 0;
			$r = $this->get_tree_navigation($this->param->id, $id); //, !(bool)$this->param->parent_id
			if ($r){
				$result = array_merge($result, $r);
			}
			// родительские элемены
			if ($this->param->parent_id){
				unset($result[0]); // удаляем название модуля для дочерней цепочки
				$parent = $this->get_module_parent_navigation($this->param->parent_id, $this->model->parent_id);
				$result = array_merge($parent, $result);
			}

			return $result;
		}
		
		public function title(){
			$id = ($this->item_id)? $this->item_id : 0;
			$map = Sitemap::instance()->get_sitemap('module');
			if (isset($map[$this->module][$id])){
				$map = $map[$this->module][$id]['translate'];
				return isset($map[$this->lang['id']])? $map[$this->lang['id']]['title'] : $map[GE::lang('id')]['title'];
			}
			return '';
		}
		
		public function menu(){
			
			return '';
		}
		
		public function keywords(){
			
			return '';
		}
		
		public function description(){
			
			return '';
		}
		
		
		
	}