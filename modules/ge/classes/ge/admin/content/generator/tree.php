<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Класс генератор контента для редактирования элементов модуля.
	 * Для модулей сайта типа "Дерево"
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
	
	class GE_Admin_Content_Generator_Tree extends GE_Admin_Content_Generator
	{
		public function __construct($module){
			parent::__construct($module);
			$this->model = GE::mod($module);
		}
		
		public function content($action = ''){
			if ($this->item_id && $action == 'edit') {
				return $this->get_item_data($this->item_id);
			}
			else if ($action == 'parent'){
				return $this->get_list_data(NULL, $this->item_id);
			}
			else {
				return $this->get_list_data();
			}
		}
		
		public function navigation(){
			
			return array();
		}
		
		public function add_item($data, $lang_id, $action){
			$validate = $this->model->validate_add($data, $this->param->caption_field);
			if ( ! $validate->check()){
				// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
				// TODO Добавить систему сообщений
				return false;
			}
			$fields = GE::fields($this->field_model, $lang_id);
			$validate_data = $validate->as_array();
			$sitemap_title = $validate_data[$this->param->caption_field];
			$processed_data = $fields->process($validate->as_array(), 'save');
			
			$data = Arr::overwrite($data, $processed_data);

			// создаем технические данные
			$data['lastmod'] = date('Y-m-d H:i:s');
			$data['user_id'] = Manageauth::instance()->get_user()->id;
			
			// сохраняем
			$this->model->values($data);
			$this->model->save();
			$item_id = $this->model->id;
			$self_parent_id = $this->model->self_parent_id;

			// добавляем информацию в карту сайта
			$map = Sitemap::instance();
			$sitemap = $this->get_sitemap_value($item_id, $sitemap_title, $self_parent_id);
			$translates = $this->get_sitemap_translate_values($lang_id, $sitemap_title);
 			$map->create_record($sitemap, $translates);
 			
			// и в конце обновляем информацию корневого раздела в sitemap
 			$map->touch($this->param->id);
			
//			$id = $data['self_parent_id'];
			$id = $this->item_id;
// 			$action = 'list';
// 			if ($this->param->parent_id && isset($data['parent_id'])){
// 				$id = $data['parent_id'];
// 				$action = 'parent';
// 			}
 			
			$redirect = URL::base().Route::get('admin_module')->uri(array(
				'module' => $this->param->name,
				'action' => $action,
				'id' => $id
			));
			
			return $redirect;
		}
		
		public function edit_list($data, $lang_id, $action){
			$map = Sitemap::instance();
			$fields = GE::fields($this->field_model, $lang_id);
/*  >> ==================================================================  */
/*  4sub  */
			$child = $this->get_child();
			if ($child){
				$child_model = GE::mod($child['name']);
			}
/*  << ==================================================================  */
			foreach ($data['title_list'] as $id => $title){
				
				$this->model->find($id);
				
				// если есть на удаление - удаляем, из карты сайта - тоже
				if (isset($data['delete_list'][$id])){
					$this->_delete_tree_element($this->model, $fields, $map);
/*  >> ==================================================================  */
/*  4sub  */
					// проверяем на подмодули, если есть дети удаляем связных
					if (isset($child_model)){
						$r = $child_model->where('parent_id', '=', $id)->find_all();
						foreach($r as $del){
							$child_model->delete($del->id);
						}
					}
/*  << ==================================================================  */
					continue;
				}

				$update = array();
				$update[$this->param->caption_field] = $title;
				$update['outorder'] = $data['outorder_list'][$id];
				$update['isactive'] = (isset($data['active_list'][$id])) ? 1 : 0;
				
				$validate = $this->model->validate_add($update, $this->param->caption_field);
				if ( ! $validate->check()){
					// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
					// TODO Добавить систему сообщений
					continue;
				}

				$processed_data = $fields->process($validate->as_array(), 'save', $this->model->as_array());
				$update = Arr::overwrite($update, $processed_data);
				
				$this->model->values($update);
				$this->model->save();

				$map->active($this->param->id, $id, $update['isactive']);
				$translates = array();
				$translates[] = array(
					'lang_id' => $lang_id,
					'caption' => $title,
				);
				$map->update_record($this->param->id, $id, array(), $translates);
			}
			
			$id = $this->item_id;
// 			$action = 'list';
// 			if ($this->param->parent_id){
// 				$action = 'parent';
// 			}

			$redirect = URL::base().Route::get('admin_module')->uri(array(
				'module' => $this->param->name,
				'action' => $action,
				'id' => $id
			));

			return $redirect;
		}
		
		public function edit_item($data, $lang_id, $action){
//_d($action); die;
			$this->model->find($this->item_id);
			// Добавляем в массив данных значения которые не передавались из БД
			foreach($this->field_model as $field => $param){
				if ( ! isset($data[$field]))
					$data[$field] = $this->model->$field;
			}
			
			$fields = GE::fields($this->field_model, $lang_id);
			$processed_data = $fields->process($data, 'save', $this->model->as_array());
			

			// сохраняем их в базе
			$processed_data['lastmod'] = date('Y-m-d H:i:s');
			$processed_data['isactive'] = (isset($data['isactive'])) ? 1 : 0;
			$processed_data['outorder'] = $data['outorder'];
			$this->model->values($processed_data)->save();

			// сохраняем пользовательские js и css
			if ($data['user_css']){
				$css = ORM::factory('customcss');
				$css->where('module_id', '=', $this->param->id)->where('item_id', '=', $this->item_id)->find();
				if (!$css->id){
					$css->clear();
					$css->module_id = $this->param->id;
					$css->item_id = $this->item_id;
				}
				$css->value = $data['user_css'];
				$css->save();
			}
			if ($data['user_js']){
				$js = ORM::factory('customjs');
				$js->where('module_id', '=', $this->param->id)->where('item_id', '=', $this->item_id)->find();
				if (!$js->id){
					$js->clear();
					$js->module_id = $this->param->id;
					$js->item_id = $this->item_id;
				}
				$js->value = $data['user_js'];
				$js->save();
			}
			
			// добавляем информацию в карту сайта
			$map = Sitemap::instance();
			$sys = $data['sys'];
			$sitemap = array(
				'module_id' => $this->param->id,
				'item_id' => $this->item_id,
//				'item_parent_id' => 0,
				'short_url' => $sys['short_url'],
				'isactive' => $processed_data['isactive'],
				'sitemap_show' => (isset($sys['sitemap_show'])) ? 1 : 0,
				'lastmod' => date('Y-m-d H:i:s'),
				'seo_priority' => $sys['priority'],
				'seo_changefreq' => $sys['changefreq'],
			);
			$translates = array();
			$translates[] = array(
				'lang_id' => $lang_id,
				'title' => $sys['title'],
				'keywords' => $sys['keywords'],
				'description' => $sys['description'],
				'caption' => $data[$this->param->caption_field],
			);
 			$map->update_record($this->param->id, $this->item_id, $sitemap, $translates);
			

 			$id = $this->model->self_parent_id;
 			$action = 'list';
 			if ($this->model->self_parent_id == 0 AND isset($this->model->parent_id)){
 				$id = $this->model->parent_id;
 				$action = 'parent';
 			}

 			$redirect = URL::base().Route::get('admin_module')->uri(array(
				'module' => $this->param->name,
				'action' => (isset($data['_back']))? $action: 'edit',
				'id' => (isset($data['_back']))? $id: $this->item_id
			));

			return $redirect;
			
		}
		
		public function title(){
			
			return 'Title of tree module';
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
		
		/**
		 * Получает данные записи по идентификатору
		 * 
		 * @param int $id
		 */
		protected function get_item_data($id){
			$data = array();

			$data['caption_field'] = $this->param->caption_field;
			
			$values = $this->model->find($id)->as_array();
			$process = $this->prerare_item($values);
			$data['fields'] = '';
			
			foreach($this->field_model as $field_name => $param){
				$field = $process[$field_name];
				$field['title'] = $this->field_model[$field_name]['title'];
				$field['name'] = $field_name;
				$data['fields'] .= (string)GE::view('admin/fields/'.$this->field_model[$field_name]['type'], $field, FALSE);
			}
				
			// стандартные значения (id, outorder, isactive)
			$default = array();
			$js = ORM::factory('customjs');
			$default['js'] = $js->get_item($this->param->id, $id);
			$css = ORM::factory('customcss');
			$default['css'] = $css->get_item($this->param->id, $id);
			$default['outorder'] = $values['outorder'];
			$default['isactive'] = ($values['isactive']) ? 'checked="true"' : '';
			$data['default'] = GE::view('admin/module/default', $default, FALSE);
			
			// системные значения
			$system = $this->get_item_system_info($this->param->id, $this->item_id, $this->lang['id']);
			$data['system'] = GE::view('admin/module/system', $system, FALSE);
			
			return GE::view($this->template_dir.'edit/cover', $data, FALSE);
		}
		
		/**
		 * Получает данные активных записей раздела
		 */
		protected function get_list_data($self_parent_id = NULL, $module_parent_id = NULL){
			
			$data = array();
			$data['caption_field'] = $this->param->caption_field;
			$data['self_parent_id'] = ($this->item_id && !$module_parent_id)? $this->item_id : 0;
/*  >> ==================================================================  */
/*  4sub  */
			$_module_parent_id = $module_parent_id;
			if ($this->param->parent_id && !$module_parent_id){
				$r = $this->model->find($this->item_id);
				$_module_parent_id = $r->parent_id;
			}
			$data['module_parent_id'] = $_module_parent_id;
/*  << ==================================================================  */
			$data['auto_outorder'] = $this->model->get_outorder((($module_parent_id)? 0 : $this->item_id), $_module_parent_id);
			$data['list'] = array();
			$list = $this->model->get_items(FALSE, $this->param->outorder, (($module_parent_id)? 0 : $this->item_id), $_module_parent_id);
			foreach($list as $item){
				$out = $this->prerare_tree_item($item);
				$out['_caption'] = htmlspecialchars($out[$this->param->caption_field], ENT_QUOTES);
				$out['_edit_href'] = Manage::get_module_url($this->param->name, 'edit', $item['id']);
				$out['_list_href'] = Manage::get_module_url($this->param->name, 'list', $item['id']);
				$out['_list_children_href'] = Manage::get_module_url($this->param->name, 'list', $item['id']);
/*  >> ==================================================================  */
/*  4sub  */
				$sub = GE::modules($this->param->id);
				if (count($sub)){
					$out['_sub_href'] = Manage::get_module_url($sub[$this->param->id]['name'], 'parent', $item['id']);
				}
/*  << ==================================================================  */
				$out['_checked'] = ($item['isactive']) ? ' checked="true" ' : '';
				$data['list'][] = $out;
			}
			
			return GE::view($this->template_dir.'list/cover', $data, FALSE);
		}
		
		/**
		 * Обрабатывает данные записи
		 * 
		 * @param $item
		 */
		protected function prerare_item($item){
			
			if ($this->field_model){
				$fields = GE::fields($this->field_model, $this->lang['id']);
				$out = $fields->process($item, 'edit');
				return $out;
			}
			return $item;
		}
		
		/**
		 * Обрабатывает данные записи элемента дерева
		 * 
		 * @param $item
		 */
		protected function prerare_tree_item($item){
			
			if ($this->field_model){
				$fields = GE::fields($this->field_model, $this->lang['id']);
				$out = $fields->process($item);
				return $out;
			}
			return $item;
		}
		
		private function _delete_tree_element($element, $fields, $map) {
			$id = $element->id;
			$fields->process($element->as_array(), 'delete');
			$this->model->delete($id);
			$map->delete_record($this->param->id, $id);
			//Если есть дочерние элементы - их тоже удаляем
			$children = $this->model->where('self_parent_id', '=', $id)->find_all()->as_array();
			foreach ($children as $key => $element) {
				$this->_delete_tree_element($element, $fields, $map);
			}
		}
		
		
	}