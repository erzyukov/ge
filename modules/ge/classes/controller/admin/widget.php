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
class Controller_Admin_Widget extends Manage {
	
	
	public function action_index(){
		
		$this->content = 'Установленные виджеты !';
	}
	
	public function action_edit(){
		
		$this->content = 'Редактируем !';
	}
	
/*	
	protected function get_sub_menu(){
		$list = '';
		$widgets = Gengine::config('widgets');
//echo Kohana::debug($widgets);
		foreach (Gengine::widget_list() as $name){
			$data['title'] = $widgets[$name]['title'];
			$data['href'] = '';
			$data['href'] = $this->get_widget_url($name);
			$list .= View::factory('admin/accessory/sub_menu_list', $data);
		}

		return View::factory('admin/accessory/sub_menu', array('list' => $list));
	}
*/
	
	
	
	
	
}



