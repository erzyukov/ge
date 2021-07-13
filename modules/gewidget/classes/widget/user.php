<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Виджет - Пользователи сайта
	 * 
	 * @package Gengine
	 * @author Atber (aka Khramkov Ivan)
	 * @copyright  (c) 2007-2011 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Widget_User extends Widget {

	protected $config = array();
	protected $auth;
	protected $user = NULL;
	private $_error = NULL;
	private $_salt = 'cookie';
	
	public function data() {
		$action = (isset($this->param[1]))? $this->param[1] : 'index';
		$this->current_template = $action;
		$this->config = Kohana::config('user');
		$this->auth = Wauth::instance();
		$user_data = ($this->auth->get_user())? $this->auth->get_user()->as_array() : NULL;
		$this->user = ($user_data)? ORM::factory('widget_user', $user_data['id']) : NULL;
		return (method_exists($this, $action))? $this->$action() : $this->index();
	}
	
	public function index(){
		$data = array();
		if (isset($_COOKIE['logged_user'])) {
			$user = ORM::factory('widget_user')->where('cookie', '=', $_COOKIE['logged_user'])->find();
			if ($user->id && !empty($user->cookie)) {
				Wauth::instance()->login($user->email, $user->password, true);
			}
		}
		$data['user'] = (Wauth::instance()->logged_in())? Wauth::instance()->get_user() : NULL;
		return $data;
	}
	
	public function register(){
		$data = array();
		$data['reg'] = $this->config['data'];
		if (isset($this->param[2])) {
			$data['credits'] = $this->param[2];
		}
		return $data;
		
	}

	public function office(){
		$data = array();
	    if (Wauth::instance()->logged_in()){
		    $data['user'] = Wauth::instance()->get_user();
			$data['config'] = Kohana::config('user.data');
		    $data['orders'] = ORM::factory('widget_order')->where('user_id', '=', $data['user']->id)->find_all()->as_array();
			unset($data['config']['email']);
		}
		else {
			Request::instance()->redirect('/');
		}
		return $data;
	}
	
	public function action_reminder(){
		if (empty($_POST)) {
			$this->request->response = View::factory('smarty:'.$this->view_dir.'reminder', array());
		}
		else {
			$data = $_POST;
			$user = ORM::factory('wuser', array('email' => $data['email']));
			if ($user->id){
				$password = substr(md5(rand(1000, 99999)), rand(1,10), 8);
				$user->values(array('password' => Wauth::instance()->hash_password($password)))->save();
				$message = '<h4>Ваш пароль востановлен</h4><p>Ваш новый пароль: '.$password.'. Чтобы поменять пароль, зайдите в личный кабинет.</p>';
				Email::send($data['email'], Gengine::config('sitemail.mail_main.address'), 'Восстановление пароля', $message, true);
				$this->request->response = json_encode(array('success' => 1));
			}
			else{
				$this->request->response = json_encode(array('error' => 'В системе не зарегистрирован такой e-mail!'));
			}
		}
	}
	
	public function adduser(){
		$result = $this->_register_validate($_POST);
		if ($result['success']) {
			$user = ORM::factory('widget_user')->set_register_data($_POST, $this->config['data']);
			$result['message'] = __('Вы успешно зарегистрированы! Вы сможете быть авторизированы на сайте после того, как администратор активирует вашу учетную запись.');
			$result['href'] = '/';
		}
		else {
			$result = array_merge(array('captcha' => Captcha::instance()->render()), $result);
		}
		return $result;
	}
	
	public function edit() {
		$id = ($this->param[2])? $this->param[2] : 0;
		$user = Wauth::instance()->get_user();
		if ($id && $user && $user->id == $id) {
			$result = $this->_edit_validate($_POST);
			if ($result['success']) {
				$user->set_edit_data($_POST, $this->config['data']);
				//Email::send(Gengine::config('sitemail.mail_main.address'), Gengine::config('sitemail.mail_main.address'), 'Зарегистрирован новый пользователь', 'Зарегистрирован новый пользователь: <a href = "http://r5.global.su/admin/wuser/$user/'.$user->id.'" title = "Посмотреть информацию по пользователю">'.$user->email.'</a>');
				$result['message'] = __('Данные успешно изменены!');
			}
		}
		else {
			return $this->_set_error(__('Неизвестный пользователь!'));
		}
		return $result;
	}


	public function check(){
		$data = array();
		if (!empty($_POST['email']) && !empty($_POST['password'])) {
			$result = ( int ) $this->auth->login($_POST['email'], Wauth::instance()->hash_password($_POST['password'], Wauth::instance()->find_salt($_POST['password'])), true);
			if ($result) {
				$user = ORM::factory('widget_user')->where('email', '=', $_POST['email'])->find();
				if (isset($_POST['remember'])) {
					$time = time() + 60 * 60 * 24 * 14;
					$cookie = md5($this->_salt.$_POST['email'].$_POST['password']).'.'.$time;
					setcookie('logged_user', $cookie, $time, '/');
					$user->cookie = $cookie;
				}
				$user->last_login = time();
				$user->save();
				return array('success' => 1, 'reload' => 1);
			}
			else {
				return array('error' => __('Неверные логин или пароль!'));
			}
		}
		else {
			return array('error' => __('Введите логин и пароль!'));
		}
	}
	
	public function login() {
		return array();
	}

	public function logout(){
		$this->user->cookie = '';
		$this->user->save();
		Wauth::instance()->logout();
		if (isset($_COOKIE['logged_user']) && !empty($_COOKIE['logged_user'])) {
			$cookie = explode('.', $_COOKIE['logged_user']);
			setcookie('logged_user', false, $cookie[1], '/');
		}
		Request::instance()->redirect(Request::$referrer);
	}

	public function action_savepassword(){
		if ($this->user_information_validate($_POST)) {
			$this->user->set_info_data($_POST, $this->config['edit']);
			$this->request->response = json_encode(array('success' => 1));
		}
		else {
			$this->request->response = json_encode(array('error' => $this->_error));
		}
	}
	
	public function action_savesubscribe(){
		if (empty($_POST))
			return false;
		$data = $_POST;

		$user_data = $this->auth->get_user()->as_array();
		$param = unserialize($user_data['data']);
		$subscribe = $data['subscribe'];
		$sprice = $data['sprice'];
		
		if ($param['subscribe'][0] != $subscribe OR $param['sprice'][0] != $sprice){

			$param['subscribe'][0] = $subscribe;
			$param['sprice'][0] = $sprice;
			
			$user = ORM::factory('wuser', $user_data['id']);
			$user->values(array('data' => serialize($param)));
			$user->save();
		}
		
		$this->request->response = '1';
	}
	
	//Валидация
	
	private function _register_validate($data) {
		if (empty($data)) {
			return $this->_set_error(__('Пустой запрос'));
		}
		$user = ORM::factory('widget_user')->where('email', '=', $data['email'])->find();
		if ($user->id) {
			return $this->_set_error(__('Пользователь с таким e-mail уже зарегистрирован!'), 'email');
		}
		try {
			Email::send('abcd@abcd.net', $data['email'], '', '');
		}
		catch (Exception $e) {
			return $this->_set_error(__('Некорректный электронный адрес!'), 'email');
		}
		foreach($this->config['data'] as $field => $properties){
			if ($properties['obl'] && empty($data[$field])) {
				return $this->_set_error(__('Поле').' '.$properties['title'].' '.__('обязательно к заполнению!'), $field);
			}
		}
		if ($data['password_confirm'] != $data['password']) {
			return $this->_set_error(__('Пароли не совпадают!'), 'password_confirm');
		}
		if (!Captcha::instance()->valid($data['captcha'])) {
			return $this->_set_error(__('Символы не совпадают!'), 'captcha');
		}
		return array('success' => 1);
	}
	
	private function _edit_validate($data) {
		if (empty($data)) {
			return $this->_set_error(__('Пустой запрос'));
		}
		if (isset($data['email'])) {
			unset($data['email']);
		}
		foreach($this->config['data'] as $field => $properties){
			if ($field == 'email') {
				continue;
			}
			if ($properties['obl'] && empty($data[$field])) {
				return $this->_set_error(__('Поле').' '.$field.' '.__('обязательно к заполнению!'), $field);
			}
		}
		if (!empty($data['password']) && !empty($data['password_confirm'])) {
			if ($data['password_confirm'] != $data['password']) {
				return $this->_set_error(__('Пароли не совпадают!'), 'password_confirm');
			}
		}
		return array('success' => 1);
	}
	
	private function _set_error($message = 'Ежики любят тебя', $field = NULL) {
		$this->_error = array('success' => 0, 'error' => $message, 'field' => $field);
		return $this->_error;
	}
}