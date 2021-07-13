<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Виджет баннерная система
	 * 
	 * @package ge
	 * @author Atber (aka Khramkov Ivan)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class Widget_Banner extends Widget {
	
//	protected $menu_name;
	protected $model;
	
	public function data(){
		
		if (isset($this->param[2]) && $this->param[2] == 'r')
			$this->redirect();
		
		$result = array();
		$list = array();
		$position = $this->param[1];
		$this->model = ORM::factory('widget_banner_list');
		$r = $this->model->where('position', '=', $position)->where('isactive', '=', 1)->find_all();
		foreach ($r as $b){
			if (
				($b->max_show != 0 AND $b->_show >= $b->max_show) OR 
				($b->max_click != 0 AND $b->_click >= $b->max_click)
			){
				$this->model->find($b->id)->isactive = 0;
				$this->model->save();
				continue;
			}
			$list[] = $b->as_array();
		}
		
		if (count($list)){
			$result = $list[rand(0, count($list)-1)];
			$types = Kohana::config('banner.type');
			$this->current_template = $types[$result['type']];
			$result['_href'] = '/wbanner/$'.$result['id'].'/$r';
			
			$this->model->clear()->find($result['id'])->_show += 1;
			$this->model->save();
		}
		return $result;
	}

	protected function redirect(){
		$this->model = ORM::factory('widget_banner_list');
		$r = $this->model->find($this->param[1]);
		$this->model->_click += 1;
		$this->model->save();
		$href = ($r->href)? $r->href: '/';
		Request::instance()->redirect($href);
		exit;
	}
	
//	protected function get_menu($menu_name){
//		$data = array();
//		$menu_list = $this->model->get_menu_list($menu_name);
//
//		if ( ! $menu_list){
//			return $data;
//		}
//		$menu = $this->model->get_element_tree($menu_list[$this->param[1]]['id']);
//		foreach($menu[0] as $item){
//			$item['href'] = ($item['direct_link'])? $item['direct_link']: GE::get_module_url(GE::lang('uri'), (int) $item['module_id'], $item['item_id']);
//			$data[] = $item;
//		}
//
//		return $data;
//	}
	
	
}