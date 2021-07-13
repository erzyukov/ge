<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * 
	 * 
	 * 
	 * @package Gengine
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Controller_Admin_Account extends Manage {
	
	public $template = 'smarty:admin/login';
	
	public $auth_required = FALSE;
	
	public function action_login(){
		
		// если залогинены, перекидываем в начало
		if(Manageauth::instance()->logged_in() !== FALSE){
			Request::instance()->redirect('admin');		
		}
		
		// если есть запрос на логин, и он не пустой
		if ($_POST)
		{
			// инициализируем нового пользователя
			$user = ORM::factory('admin_user');
 
			// пытаемся залогинить пользователя
			$status = $user->login($_POST);
 
			// если пользователя залогинили
			if ($status){
				// перенаправляем в начало
				Request::instance()->redirect('admin');		
			}
			else{
// TODO зарегистрировать ошибку через сообщения, класс обработки сообщений надо написать и встроить
				// если ошибка, отображаем ошибку
				foreach ($_POST->errors('signin') as $id => $desc){
					$this->message .= '<b>'.$id.'</b>: '.$desc.'<br />';
				}
				//$this->message = $_POST->errors('signin');
			}
 
		}
		
		$this->content = View::factory('smarty:admin/login');
	}
	
	public function action_logout(){
		
		// отключаем пользователя
		Manageauth::instance()->logout();
 
		// посылаем на страницу входа
		Request::instance()->redirect('admin/login');		
	}
	
	
}