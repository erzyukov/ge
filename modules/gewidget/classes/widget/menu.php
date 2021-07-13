<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Виджет меню
	 * 
	 * @package ge
	 * @author Atber (aka Khramkov Ivan)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class Widget_Menu extends Widget {
	
	protected $menu_name;
	protected $model;
	protected $module_id = 0;
	protected $item_id = 0;
	
	public function data(){
		$menu_name = $this->param[1];
		$this->module_id = (isset($this->param[2]))? $this->param[2]: 0;
		$this->item_id = (isset($this->param[3]))? $this->param[3]: 0;
		$this->model = ORM::factory('widget_menu');
		$this->current_template = $menu_name;
		
		return $this->get_menu($menu_name);
	}

	protected function get_menu($menu_name){
		$data = array();
		$menu_list = $this->model->get_menu_list($menu_name);

		if ( ! $menu_list){
			return $data;
		}
		$menu = $this->model->get_element_tree($menu_list[$this->param[1]]['id']);
		foreach($menu[0] as $item){
			$item['active'] = 0;
			$item['href'] = ($item['direct_link'])? $item['direct_link']: GE::get_module_url($this->lang['uri'], (int) $item['module_id'], $item['item_id']);
			if ($item['direct_link'] == '/' AND $this->item_id == 0 AND $this->module_id == 0){
				$item['active'] = 1;
			}
			else if ($item['module_id'] == $this->module_id AND (!$item['item_id'] OR $item['item_id'] == $this->item_id)){
					$item['active'] = 1;
			}
			
			$data[] = $item;
		}

		return $data;
	}
	
	
}