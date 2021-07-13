<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Виджет - Пользователи сайта
	 * 
	 * @package Gengine
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Controller_Widget_Wuser extends Controller {

	protected $config = array();
	protected $auth;
	protected $user = NULL;
	private $_error = NULL;
	private $_salt = 'cookie';
	
	public function __construct($request){echo "sss";
		parent::__construct($request);
		$this->config = Kohana::config('user');
		$this->auth = Wauth::instance();
		$user_data = ($this->auth->get_user())? $this->auth->get_user()->as_array() : NULL;
		$this->user = ($user_data)? ORM::factory('widget_user', $user_data['id']) : NULL;
	}
	
	public function action_index(){
		$data = array();
		if (isset($_COOKIE['logged_user'])) {
			$user = ORM::factory('wuser')->where('cookie', '=', $_COOKIE['logged_user'])->find();
			if ($user->id && !empty($user->cookie)) {
				Wauth::instance()->login($user->email, $user->password, true);
			}
		}
		if (Wauth::instance()->logged_in()){
			$data = Wauth::instance()->get_user()->as_array();
			$this->request->response = View::factory('smarty:'.$this->view_dir.'logged', $data);
		}
		else{
			$this->request->response = View::factory('smarty:'.$this->view_dir.'cover', $data);
		}
		
	}
	
	public function action_min() {
	}
	
	public function action_register(){
		$data = array();
		$data['months'] = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
		$data['year_start'] = 1930;
		$data['captcha'] = Captcha::instance();
		$this->request->response = (isset($_GET['no_json']))?
			View::factory('smarty:'.$this->view_dir.'register', $data) : 
			json_encode(array('success' => 1, 'text' => (string)View::factory('smarty:'.$this->view_dir.'register', $data)));
		
	}

	public function action_office(){
	    if (Wauth::instance()->logged_in()){
	        $data = array();
		    $data['user'] = Wauth::instance()->get_user();
		    $data['tab'] = ($this->request->param('p1'))? $this->request->param('p1') : 'orders';
		    $page = ($this->request->param('p2'))? $this->request->param('p2') : 1;
		    switch ($data['tab']) {
		    	case 'manage':
		    	    $data['fields'] = $this->generate_manage_fields($data['user']);
		    	    break;
		    	case 'history':
		    		$data['orders'] = Request::factory('/widget/wesorder/list/history/'.$page)->execute();
		    		break;
				case 'orders':
		    		$data['orders'] = Request::factory('/widget/wesorder/list/office/'.$page)->execute();
		    		break;
		    	default:
		    		$data['addrs'] = Request::factory('/widget/waddress/list')->execute();
		    }
			$this->request->response = View::factory('smarty:'.$this->view_dir.'office', $data);
		}
		else{
			$this->request->response = '';
		}
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
	
	public function action_adduser(){
		if ($this->register_validate($_POST)) {
			$user = ORM::factory('wuser')->set_register_data($_POST, $this->config['data']);
			Email::send(Gengine::config('sitemail.mail_main.address'), Gengine::config('sitemail.mail_main.address'), 'Зарегистрирован новый пользователь', 'Зарегистрирован новый пользователь: <a href = "http://r5.global.su/admin/widget/wuser/user/'.$user->id.'" title = "Посмотреть информацию по пользователю">'.$user->email.'</a>');
			//$this->request->response = json_encode();
		}
		else {
			$this->request->response = json_encode(array('error' => $this->_error));
		}
	}


	public function action_check(){
		if (isset($_POST['login']) && isset($_POST['password'])) {
			$result = ( int ) $this->auth->login($_POST['login'], $_POST['password']);
			if ($result && isset($_POST['remember'])) {
				$time = time() + 60 * 60 * 24 * 14;
				$cookie = md5($this->_salt.$_POST['login'].$_POST['password']).'.'.$time;
				setcookie('logged_user', $cookie, $time, '/');
				$user = ORM::factory('wuser')->where('email', '=', $_POST['login'])->find();
				$user->cookie = $cookie;
				$user->save();
			}
			$this->request->response = ($result)? json_encode(array('success' => 1)) : json_encode(array('error' => 'Неверные логин или пароль!'));
		}
		else {
			$this->request->response = json_encode(array('error' => 'Введите логин и пароль!'));
		}
	}

	public function action_logout(){
		$this->user->cookie = '';
		$this->user->save();
		Wauth::instance()->logout();
		if (isset($_COOKIE['logged_user']) && !empty($_COOKIE['logged_user'])) {
			$cookie = explode('.', $_COOKIE['logged_user']);
			setcookie('logged_user', false, $cookie[1], '/');
		}
		$session = & Session::instance('database')->as_array();
		if (isset($session['basket'])) {
			unset($session['basket']);
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

	public function generate_register_fields($legal, $client = NULL){
		$i = 0;
		$result = '';
		$from = $this->config['data'][(($legal)? 'legal_client' : 'private')];
		foreach($from as $field){
		    switch ($field['type']){
				case 'select':
					$option = '';
					foreach($field['values'] as $id => $value){
						$option .= '<option value="'.$id.'">'.$value.'</option>';
					}
					$field['option'] = $option;
					break;
			}
			if (in_array($field['field'], $this->config['address']['kladr'])){
				$field['type'] = 'kladr_'.$field['field'];
			}
			$result .= View::factory('smarty:'.$this->view_dir.'fields/'.$field['type'], $field);
		}
		if ($legal && $client === FALSE) {
		    foreach($this->config['data']['legal'] as $field){
		        switch ($field['type']){
				    case 'select':
					    $option = '';
				 	    foreach($field['values'] as $id => $value){
						    $option .= '<option value="'.$id.'">'.$value.'</option>';
					    }
					    $field['option'] = $option;
					    break;
			    }
				if (in_array($field['field'], $this->config['address']['kladr'])){
					$field['type'] = 'kladr_'.$field['field'];
				}
			    $result .= View::factory('smarty:'.$this->view_dir.'fields/'.$field['type'], $field);
		    }
		}
		foreach($this->config['data']['common'] as $field){
			switch ($field['type']){
				case 'select':
					$option = '';
					foreach($field['values'] as $id => $value){
						$option .= '<option value="'.$id.'">'.$value.'</option>';
					}
					$field['option'] = $option;
					break;
			}
			$result .= View::factory('smarty:'.$this->view_dir.'fields/'.$field['type'], $field);
		}
		
		return $result;
	}
	
    public function generate_manage_fields($user){
    	$i = 0;
		$result = '';
		$from = $this->config['edit'][(($user->islegal)? 'legal' : 'private')];
		foreach($from as $field){
		    switch ($field['type']){
				case 'password':
					$field['value'] = '';
					break;
				default: 
					$field['value'] = (isset($user->$field['field']))? $user->$field['field'] : '';
		    }
			$result .= View::factory('smarty:'.$this->view_dir.'fields/'.$field['type'], $field);
		}
		return $result;
	}
	
	//Валидация
	
	public function action_rvalidate() {
		if ($this->register_validate($_POST)) {
			$this->request->response = json_encode(array('success' => 1));

		}
		else {
			$this->request->response = json_encode(array('error' => $this->_error));
		}
	}
	
	protected function register_validate($data) {
		if (empty($data)) {
			$this->_set_error('Пустой запрос');
			return false;
		}
		$r = DB::select('id')->from(Gengine::wpref().'user')->where('email', '=', $data['email'])->execute()->as_array();
		if (count($r)) {
			$this->_set_error('Пользователь с таким e-mail уже зарегистрирован!');
			return false;
		}
		$arr = array();
		$arr[] = array('obl' => 1, 'field' => 'email', 'title' => 'E-Mail');
		$arr[] = array('obl' => 1, 'field' => 'password', 'title' => 'Пароль');
		foreach($this->config['data']['common'] as $field){
			$arr[] = $field;
		}
		foreach($this->config['data']['private'] as $field){
			$arr[] = $field;
		}
		$arr[] = array('obl' => 1, 'field' => 'captcha', 'title' => 'Тест Тьюринга', 'step' => 1);
		foreach($arr as $key => $field){
			if (!isset($field['step']) || $field['step'] == $data['step']) {
				if ($field['obl'] && empty($data[$field['field']])) {
					$this->_set_error('Поле '.$field['title'].' обязательно к заполнению!', $field['field']);
					return false;	
				}
				$error = NULL;
				if (isset($data[$field['field']])) {
					if (!empty($data[$field['field']]) && !ORM::factory('wuser')->validate($field['field'], $data[$field['field']], $error)) {
						$this->_set_error($error, $field['field']);
						return false;
					}
				}
			}
		}
		if ($data['password_confirm'] != $data['password']) {
			$this->_set_error('Пароли не совпадают!', 'password_confirm');
			return false;
		}
		if ($data['step']) {
			$addr_c = new Controller_Widget_Waddress($this->request);
			$result = $addr_c->register_validate($data);
			if (!$result['result']) {
				$this->_error = $result['error'];
				return false;
			}
		}
		return true;
	}
	
	protected function user_information_validate($data) {
		if (empty($data)) {
			$this->_set_error('Пустой запрос');
			return false;
		}
		if (!empty($data['password_confirm']) || !empty($data['password'])) {
			if ($this->auth->check_password($data['old_password'])) {
				if ($data['password_confirm'] != $data['password']) {
					$this->_set_error('Пароли не совпадают!', 'password_confirm');
					return false;
				}
			}
			else {
				$this->_set_error('Старый пароль неверен', 'old_password');
				return false;
			}
		}
		$from = $this->config['edit'][(($this->user->islegal)? 'legal' : 'private')];
		foreach($from as $field){
			if ($field['obl'] && empty($data[$field['field']])) {
				if (isset($data['address']) && empty($data['address']) && in_array($field['field'], $this->config['address']['kladr'])) {
					$this->_set_error('Информация об адресе должна быть заполнена полностью!');
				}
				elseif (isset($data['address']) && !empty($data['address']) && in_array($field['field'], $this->config['address']['kladr'])) {
					continue;
				}
				$this->_set_error('Поле '.$field['title'].' обязательно к заполнению!', $field['field']);
				return false;	
			}
			$error = NULL;
			if (isset($data[$field['field']])) {
				if (!empty($data[$field['field']]) && !ORM::factory('wuser')->validate($field['field'], $data[$field['field']], $error)) {
					$this->_set_error($error, $field['field']);
					return false;
				}
			}
		}
		return true;
	}
	
	private function _set_error($message = 'Неожиданная ошибка', $field = NULL) {
		$this->_error = array('message' => $message, 'field' => $field);	
	}
}