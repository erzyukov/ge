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
class Controller_Admin_Widget_User extends Manage {
	
	protected $view_dir = 'smarty:admin/widget/user/';
//	protected $sitemap; 
	protected $model = NULL;
	protected $post_error = array();
	protected $errors = array();
	protected $id = NULL;
	protected $user_login = '';
	
	protected $folder = '/rs/users/';
	
	protected function init(){
		$this->model = ORM::factory('widget_user');
		$this->id = ($this->request->param('id'))? $this->request->param('id') : 0;
		$this->folder = $_SERVER['DOCUMENT_ROOT'].$this->folder;
		
		parent::init();
	}

	public function bind_cargo($data){
		$r = ORM::factory('widget_cargo_tracker')->where('number', '=', $data['number'])->find_all()->as_array();
		if (count($r)){
			foreach ($r as $item){
				$item->user_id = $this->id;
				$item->save();
			}

			return URL::base().Route::get('admin_widget')->uri(array(
				'controller' => $this->request->controller,
				'action' => 'data',
				'id' => $this->id,
			));
		}

		$this->errors[] = 'Груз с таким номером не найден!';
		return false;
	}
	
    public function add_user($data){
    	$validate = $this->model->validate_add($data);
    	
    	if ($validate->check()) {
			$this->model->add_user($validate->as_array());
			
			return URL::base().Route::get('admin_widget')->uri(array(
				'controller' => $this->request->controller,
				'action' => 'edit',
			));
    	}
    	else{
    		$this->post_error = $validate->errors();
    		return false;
    	}
	}
	
    public function save_user($data){
		$this->model->find($this->id);
    	$this->user_login = $data['login'];
    	
		$default = unserialize($this->model->data);
    	$conf = Kohana::config('user.data');
		foreach($conf as $field => $p){
			$data['data'][$field] = $this->prepare_save_field($field, (isset($data['data'][$field]))? $data['data'][$field]: '', $p['type'], (isset($default[$field]))?$default[$field]:'');
		}

		if (count($this->errors)){
    		return false;
    	}
    	
    	$data['data'] = serialize($data['data']);
		
    	if ($this->model->login == $data['login']){
			unset($data['login']);
		}
    	$validate = $this->model->validate_save($data);
    	if ($validate->check()) {
    		$values = $validate->as_array();
    		if (!$values['login']) unset($values['login']);
    		if (!$values['password']) unset($values['password']);
    		$this->model->values($values)->save();
			
			return URL::base().Route::get('admin_widget')->uri(array(
				'controller' => $this->request->controller,
				'action' => 'edit',
			));
    	}
    	else{
    		$this->post_error = $validate->errors();
    		return false;
    	}
	}
	
	protected function prepare_save_field($field, $value, $type, $default = ''){
    	$allow = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
		$result = '';
		switch ($type) {
			case 'text':
			case 'int':
				$result = $value;
				break;
			case 'date':
				$d = new DateTime($value);
				$result = $d->format('U');
				break;
			case 'image':
				$result = $default;
		    	$file = $this->get_aplod_from_array('data', $field);
		    	
    			if (!$file['name']) break;
    			$fname = $this->user_login.'_'.$field;

    			$img_type = explode('.', $file['name']);
    			$img_type = $img_type[count($img_type)-1];

    			if (!in_array(strtolower($img_type), $allow)){
		    		$this->errors[] = 'Неверный формат изображений!';
					break;
    			}
		    	if (!Upload::not_empty($file)){
		    		$this->errors[] = 'Не удалось загрузить изображения!';
					break;
		    	}
    			Upload::save($file, $fname.'.'.$img_type, $this->folder, 0755);
				$result = $fname.'.'.$img_type;
				break;
		}
		return $result;
	}
	
	protected function get_aplod_from_array($array_name, $var){
		$result = array();
		if (isset($_FILES[$array_name])){
			foreach ($_FILES[$array_name] as $key => $value){
				$result[$key] = $value[$var];
			}
		}
		return $result;
	}

	protected function delete_user($data){
		$this->model->find($data['id']);
		$this->model->delete();
		return URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
		));
	}
	
	
//    public function action_edituser(){
//		$id = ($this->request->param('id'))? $this->request->param('id') : 0;
//		$this->model->edit_user($_POST, $id);
//		$this->request->redirect($this->post_redirect);
//	}
	
	public function action_edit(){
		$data = array();
//		$data['data'] = Kohana::config('user.data');
		$r = ORM::factory('widget_user')->order_by('id', 'DESC')->find_all();
		foreach ($r as $item){
			$item = $item->as_array();
			$item['last_login'] = ($item['last_login'])? date('d.m.Y H:i', $item['last_login']): 'Не посещал';
			$d = unserialize($item['data']);
			$item['fio'] = (isset($d['fio']))?$d['fio']:'-';
//			$item['mail'] = (isset($d['mail']))?$d['mail']:'-';
			$item['number'] = (isset($d['number']))?$d['number']:'-';
//			$t = unserialize($item['ticket']);
//			$item['expire'] = (isset($t['expire']))? date('d.m.Y', $t['expire']): 'отсутствует';
			$data['list'][] = $item;
		}
		$data['errors'] = $this->errors;
		$this->content = View::factory($this->view_dir.'list', $data);
	}

	public function action_data(){
		$data = array();
		
		$data['conf'] = Kohana::config('user.data');
		$r = $this->model->find($this->id);
		$data['login'] = $r->login;
		$data['mail'] = $r->mail;
		$values = ($s = unserialize($r->data))? $s: array();
		$data['values'] = array();
		
		foreach($data['conf'] as $field => $p){
			$data['field'][$field] = $this->prepare_view_field($field, (isset($values[$field]))? $values[$field]: '', $p['type']);
		}
		
		
		$r = ORM::factory('widget_cargo_tracker')->where('user_id', '=', $this->id)->order_by('id', 'ASC')->find_all()->as_array();
		foreach($r as $item){
			$data['list'][$item->number] = $item;
		}
		
		$data['errors'] = $this->errors;
		$this->content = View::factory($this->view_dir.'data', $data);
	}

	protected function prepare_view_field($field, $value, $type){
//		return View::factory($this->view_dir.'_'.$type, array('field' => $field, 'value' => $value));

		$result = '';
		switch ($type) {
			case 'text':
				$result = View::factory($this->view_dir.'_text', array('field' => $field, 'value' => $value));
				break;
			case 'date':
				$value = date('d.m.Y', (int)$value);
				$result = View::factory($this->view_dir.'_date', array('field' => $field, 'value' => $value));
				break;
			case 'int':
				$result = View::factory($this->view_dir.'_int', array('field' => $field, 'value' => $value));
				break;
			case 'image':
				$result = View::factory($this->view_dir.'_image', array('field' => $field, 'value' => $value));
				break;
		}
		return $result;

	}
	
	
	
	//	public function action_user(){
//		$data = array();
//		$id = ($this->request->param('id'))? $this->request->param('id') : 0;
//		$data['user'] = ORM::factory('widget_user', $id)->as_array();
//		$data['data'] = Kohana::config('user.data');
//		unset($data['data']['email']);
//		$this->content = View::factory($this->view_dir.'user', $data);
//	}
//	
//	public function action_usersedit(){
//		foreach (ORM::factory('widget_user')->find_all() as $key => $user) {
//			if (isset($_POST['delete_list'][$user->id])) {
//				$user->delete();
//				continue;
//			}
//			$user->isactive = (isset($_POST['active_list'][$user->id]));
//			$user->save($user->id);
//		}
//		$this->request->redirect($this->post_redirect);
//		
//	}

/*	
	public function generate_fields($data){
		$result = '';
		$from = ($data['islegal'])? 
		    Kohana::config('wuser.admin_data.legal_client') : 
		    Kohana::config('wuser.admin_data.private');
		foreach($from as $field){
			switch ($field['type']){
				case 'text':
				case 'mail':
				case 'date':
				case 'textarea':
				case 'checkbox':
					$field['value'] = $data[$field['field']];
					break;
				case 'select':
					$field['value'] = $field['values'][$data[$field['field']]];
					break;
				case 'password':
					break;
			}
			$result .= View::factory($this->view_dir.'fields/'.$field['type'], $field);
		}
		if (!empty($data['kpp'])) {
		    foreach(Kohana::config('wuser.admin_data.legal') as $field){
			    switch ($field['type']){
				    case 'text':
				    case 'mail':
			   	    case 'date':
				    case 'textarea':
				    case 'checkbox':
					    $field['value'] = $data[$field['field']];
					    break;
				    case 'select':
					    $field['value'] = $field['values'][$data[$field['field']]];
					    break;
				    case 'password':
					    break;
			    }
			    $result .= View::factory($this->view_dir.'fields/'.$field['type'], $field);
		    }
		}
	    foreach(Kohana::config('wuser.admin_data.common') as $field){
			switch ($field['type']){
				case 'text':
				case 'mail':
				case 'date':
				case 'textarea':
				case 'checkbox':
					$field['value'] = $data[$field['field']];
					break;
				case 'select':
					$field['value'] = $field['values'][$data[$field['field']]];
					break;
			}
			$result .= View::factory($this->view_dir.'fields/'.$field['type'], $field);
		}
		
		return $result;
	}
*/	
//	protected function send_user_active($user){
//		$message = '<p>Здравствуйте!</p><p>Вы проходили регистрацию на сайте <a href = "http://rautdv.ru" title = "Перейти на сайт">Раут</a> и указали в качестве своей электронной почты следующее: <b>'.$user->email.'</b>.</p><p>Спешим вас уведомить, что ваш аккаунт успешно активирован!</p><p>Теперь вы можете авторизироваться на сайте. В качестве логина используйте указанную вами электронную почту.';
//		Email::send($user->email, Gengine::config('sitemail.mail_main.address'), 'Активация аккаунта', $message, true);
//	}

	
//	protected function get_field_by_name($name){
//		if (isset($this->field[$name]))
//			return $this->field[$name];
//		foreach (Kohana::config('wuser.data') as $field){
//			if ($field['field'] == $name){
//				$this->field[$name] = $field;
//				return $field;
//			}
//		}
//	}
	

	protected function post_action($post){
		if (isset($post['action'])){
			if (method_exists($this, $post['action'])){
				$action = $post['action'];
				unset($post['action']);
				$redirect = $this->$action($post, $this->lang_id, $this->request->action);

				if ($redirect)
					Request::instance()->redirect($redirect);
				else{
					$this->parse_error();
				}
			}
		}
		
	}
	
	protected function parse_error(){
		foreach($this->post_error as $field => $p){
			switch ($field){
				case 'login':
						if ($p[0] == 'not_empty')
							$this->errors[] = 'Логин не должен быть пустым';
						else if ($p[0] == 'min_length')
							$this->errors[] = 'Логин не должен быть короче '.$p[1][0].' символов';
						else if ($p[0] == 'max_length')
							$this->errors[] = 'Логин не должен быть длиньше '.$p[1][0].' символов';
						else if ($p[0] == 'regex')
							$this->errors[] = 'Логин должен состоять из латинских букв, цифр и знака "_"';
						else if ($p[0] == 'login_available')
							$this->errors[] = 'Пользователь с таким логином уже зарегистрирован';
						else
							$this->errors[] = 'Неизвестная ошибка: '.$field.'->'.$p[0];
					break;
				case 'password':
						if ($p[0] == 'not_empty')
							$this->errors[] = 'Пароль не должен быть пустым';
						if ($p[0] == 'min_length')
							$this->errors[] = 'Пароль не должен быть короче '.$p[1][0].' символов';
						if ($p[0] == 'max_length')
							$this->errors[] = 'Пароль не должен быть длиньше '.$p[1][0].' символов';
						else
							$this->errors[] = 'Неизвестная ошибка: '.$field.'->'.$p[0];
					break;
				default:
					$this->errors[] = 'Неизвестная ошибка: '.$field.'->'.$p[0];
			}
		}
	}
	
	
}



