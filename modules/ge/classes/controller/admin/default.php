<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 *
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Controller_Admin_Default extends Manage {
	
	
	public function action_index(){
		$this->content = 'Добро пожаловать.';
	}
	
	
	
}