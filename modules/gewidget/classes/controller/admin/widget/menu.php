<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 *
	 * 
	 * @package Gengine
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Controller_Admin_Widget_Menu extends Manage {
	
	protected $view_dir = 'smarty:admin/widget/menu/';
	protected $current_id = '';
	protected $sitemap; 
	
	protected $model = NULL;
	protected $element_tree = NULL;
	
	protected function init(){
		$this->model = ORM::factory('widget_menu');
		$id = $this->request->param('id');
		$this->current_id = ($id) ? $id : $this->model->get_first_id();
		$this->sitemap = Sitemap::instance()->get_sitemap('module_id');
		$this->element_tree = $this->model->get_element_tree($this->current_id);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
			'id' => $this->current_id
		));
		parent::init();
	}
	
	public function action_edit(){
		$data = array();
		$data['menu_option'] = $this->get_menu_option($this->current_id);
		$data['element_option'] = $this->get_element_option();
		$data['element_list'] = $this->get_element_list();
		
		$data['module_option'] = $this->get_module_option();
		$data['items_string'] = $this->get_items_string();
		$data['modules_string'] = $this->get_modules_string();
		$data['menu_id'] = $this->current_id;

		$this->content = View::factory($this->view_dir.'cover', $data);
	}
	
	
	public function add_menu($data){
		$validate = $this->model->validate_create($data);
		if (! $validate->check()){
			// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
			// TODO Добавить систему сообщений
			return false;
		}
		$data = $validate->as_array();
		$id = $this->model->add_menu($data);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
			'id' => $id
		));
	}
	
	public function delete_menu($data){
		$this->model->delete_menu($this->current_id);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
		));
	}
	
	protected function add_element($data){
		$validate = $this->model->elements->validate_create($data);
		if ( ! $validate->check() ){
			// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
			// TODO Добавить систему сообщений
			return false;
		}
		
		$data = Arr::overwrite($data, $validate->as_array());
		$this->model->elements->add_element($data);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
			'id' => $this->current_id
		));
	}
	
	protected function save_menu($data){
		$this->model->elements->save_elements($data);
	}
	
	
	protected function get_element_list(){
		return $this->generate_element_tree_list();
	}
	
	protected function generate_element_tree_list($parent_id = 0, $deep = 0){
		$result = '';
		$modules = GE::modules();
//		$lang = GE::get_lang($this->lang_id);
		$lang = GE::lang();
		if (isset($this->element_tree[$parent_id])){
			foreach ($this->element_tree[$parent_id] as $id => $element){
				$element['padding'] = (20 * $deep) + 10;
				if ($element['direct_link']){
					$element['href'] = $element['direct_link'];
				}
				else {
					if ($element['module_id']){
						if (isset($this->sitemap[$element['module_id']])){
							
							$map = $this->sitemap[$element['module_id']];
	
//							$lang_id = (isset($modules[$element['module_id']]['translate'][$this->lang_id])) ? $this->lang_id : GE::lang('id');
							$lang_id = GE::lang('id');
							$element['module'] = $modules[$element['module_id']]['translate'][$lang_id]['title'];
							
							if (isset($map[$element['item_id']])){
								
								$element['item'] = ($element['item_id'] == 0)? '* Module Root *': $map[$element['item_id']]['translate'][$lang_id]['title'];
								$element['item'] = (mb_strlen($element['item'], 'UTF-8') > 20) ? mb_substr($element['item'], 0, 20, 'UTF-8').'...' : $element['item'];
								$element['href'] = GE::get_module_url($lang['uri'], $element['module_id'], (int) $element['item_id']);
								
							}
							else{
								$element['item'] = '* ! Page not found ! *';
								$element['href'] = '';
							}
						}
						else{
							$element['module']	= '* ! Module not found ! *';
							$element['item']	= '';
							$element['href']	= '';
						}
					}
				}
				$result .= View::factory($this->view_dir.'element', $element);
				if (isset($this->element_tree[$id]))
					$result .= $this->generate_element_tree_list($id, $deep+1);
			}
		}
		return $result;
	}
	
	protected function get_element_option(){
		return $this->generate_element_tree_option();
	}
	
	protected function generate_element_tree_option($parent_id = 0, $deep = 0){
		$result = '';
		if (isset($this->element_tree[$parent_id])){
			foreach ($this->element_tree[$parent_id] as $id => $element){
				$padding = 20 * $deep;
				$result .= '<option value="'.$element['id'].'" style="padding-left: '.$padding.'px;">'.$element['title'].'</option>';
				if (isset($this->element_tree[$id]))
					$result .= $this->generate_element_tree_option($id, $deep+1);
			}
		}
		return $result;
	}
	
	protected function get_menu_option($id){
		$result = '';
		$list = $this->model->get_menu_list();
		if ($list){
			foreach($list as $menu){
				$selected = ($menu['id'] == $id)? 'selected="true"': '';
				$href = URL::base().Route::get('admin_widget')->uri(array(
						'controller' => $this->request->controller,
						'action' => $this->request->action,
						'id' => $menu['id']
					));
				$result .= '<option value="'.$href.'" '.$selected.'>'.$menu['title'].'</option>';
			}
		}
		return $result;
	}
	
	protected function get_module_option(){
		$result = '';
		$modules = GE::modules();
		foreach($modules as $module){
			$map = $this->sitemap[$module['id']][0];
			if ($map['module']['parent_id'])
				continue;

			$child = GE::modules($module['id']);
			
			$lang_id = (isset($module['translate'][$this->lang_id]))? $this->lang_id : GE::lang('id');
			$result .= '<option value="'.$module['id'].'">'.$module['translate'][$lang_id]['title'].'</option>';
			if (isset($child[$module['id']])){
				$child = $child[$module['id']];
				$lang_id = (isset($child['translate'][$this->lang_id]))? $this->lang_id : GE::lang('id');
				$result .= '<option value="'.$child['id'].'"> &nbsp; &nbsp; '.$child['translate'][$lang_id]['title'].'</option>';
			}
		}
		return $result;
	}
	
	protected function get_modules_string(){
		$result = array();
		$modules = GE::modules();
		foreach($modules as $module){

			if ($module['parent_id'])
				continue;
			$result[] = array(
				'id' => $module['id'],
				't' => $module['translate'][GE::lang('id')]['title'],
				'p' => 20
			);
			$child = GE::modules($module['id']);
			if (isset($child[$module['id']])){
				$result[] = array(
					'id' => $child[$module['id']]['id'],
					't' => $child[$module['id']]['translate'][GE::lang('id')]['title'],
					'p' => 40
				);
			}
		}
//_d($result);
		return json_encode($result);
	}

	protected function generate_modules_tree_string($items, $model, $parent_id = 0, $deep = 1){
		
	}
	
	protected function get_items_string(){
		$result = array();
		$tree_map = Sitemap::instance()->get_sitemap('tree');
		$module_param = GE::modules(0, FALSE);
		
		
		foreach(GE::module_list() as $module){
			if ($module_param[$module]['model'] != ''){
				$model = GE::mod($module);
				$r = $this->generate_items_tree_string($tree_map[$module], $model);
				$result = array_merge($result, $r);
			}
		}
		
		return json_encode($result);
	}
	
	protected function generate_items_tree_string($items, $model, $parent_id = 0, $deep = 1){

		$result = array();
		if (isset($items[$parent_id])){
			foreach ($items[$parent_id] as $id => $element){

				if ($mod_model = unserialize($element['module']['model'])){
					$fields = GE::fields($mod_model, $this->lang_id);
					$out = $fields->process($model->find($id)->as_array());
					$title = $out[$element['module']['caption_field']];
				}
				else{
					$title = (isset($element['translate'][$this->lang_id]))? $element['translate'][$this->lang_id]['title']: $element['translate'][GE::lang('id')]['title'];
				}
				$title = (mb_strlen($title, 'UTF-8') > 20) ? mb_substr($title, 0, 20, 'UTF-8').'...' : $title;
				$padding = 20 * $deep;
				$result[] = array(
					'mid' => $element['module_id'],
					'iid' => $element['item_id'],
					't' => $title, 
					'p' => $padding
				);
				if (isset($items[$id])){
					$r = $this->generate_items_tree_string($items, $model, $id, $deep+1);
					$result = array_merge($result, $r);
				}
			}
		}
		return $result;
		
	}
	
	protected function post_action($post) {
		if (isset($post['action'])){
			if (method_exists($this, $post['action'])){
				$action = $post['action'];
				unset($post['action']);
				$this->$action($post);
				// Здесь необходимо сделать проверку на наличие сообщений
				// т.к. могла возникнуть ошибка, если нет ошибок, перенаправляем куда надо
				//if (!$this->GetMessageCount())
				Request::instance()->redirect($this->post_redirect);
			}
		}
	}
	
}