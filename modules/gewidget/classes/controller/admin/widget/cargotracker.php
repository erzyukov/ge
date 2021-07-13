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
class Controller_Admin_Widget_Cargotracker extends Manage {
	
	protected $view_dir = 'smarty:admin/widget/cargotracker/';
//	protected $current_id = '';
//	protected $sitemap; 
	
	protected $model = NULL;
	protected $item_id = NULL;
//	protected $element_tree = NULL;
	
	protected function init(){
		$this->model = ORM::factory('widget_cargo_tracker');
		$this->item_id = $this->request->param('id', NULL);
		$this->js = array();
		$this->js[] = '/rs/admin/js/jquery.js';
		$this->js[] = '/rs/admin/js/jquery.form.js';
		$this->js[] = '/rs/admin/js/jquery.ui.js';
		$this->js[] = '/rs/admin/js/cargotracker.js';
		
//		$this->current_id = ($id) ? $id : $this->model->get_first_id();
//		$this->sitemap = Sitemap::instance()->get_sitemap('module_id');
//		$this->element_tree = $this->model->get_element_tree($this->current_id);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
//			'id' => $this->current_id
		));
		parent::init();
	}
	
	public function action_edit(){
		$data = array();
		
		$data['list'] = $this->model->order_by('id', 'DESC')->limit(100)->find_all()->as_array();
		
		$data['action'] = json_encode($this->get_refefence('action'));
		$data['dest'] = json_encode($this->get_refefence('destination'));
		
		$data['search_error'] = (isset($_GET['s']) AND $_GET['s'] == 'no')? 1: 0;
		
		$this->content = View::factory($this->view_dir.'cover', $data);
	}

	public function action_action(){
		$data = array();
		$data['list'] = ORM::factory('widget_cargo_action')->find_all()->as_array();
		$this->content = View::factory($this->view_dir.'action', $data);
	}

	public function action_destination(){
		$data = array();
		$data['list'] = ORM::factory('widget_cargo_destination')->find_all()->as_array();
		$this->content = View::factory($this->view_dir.'destination', $data);
	}
	
	public function action_view(){
		$data = array();
		$r = $this->model->find($this->item_id);
		$number = $r->number;
		$this->model->clear();
		$all = $this->model->where('number', '=', $number)->order_by('id', 'DESC')->find_all()->as_array();
//_d($all);
		$data['value'] = $all[0];
		unset($all[0]);
		$data['prev'] = $all;

		
//		_d($r->number);
		
		$data['action'] = json_encode($this->get_refefence('action'));
		$data['dest'] = json_encode($this->get_refefence('destination'));
		
		$this->content = View::factory($this->view_dir.'view', $data);
	}
	
	
	
	protected function get_refefence($name){
		$result = array();
		$r = ORM::factory('widget_cargo_'.$name)->find_all();
		foreach($r as $item)
			$result[] = $item->value;
		return $result;
	}

//	public function add_menu($data){
//		$validate = $this->model->validate_create($data);
//		if (! $validate->check()){
//			// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
//			// TODO Добавить систему сообщений
//			return false;
//		}
//		$data = $validate->as_array();
//		$id = $this->model->add_menu($data);
//		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
//			'controller' => $this->request->controller,
//			'action' => 'edit',
//			'id' => $id
//		));
//	}
	
//	public function delete_menu($data){
//		$this->model->delete_menu($this->current_id);
//		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
//			'controller' => $this->request->controller,
//			'action' => 'edit',
//		));
//	}

	
	// ======================================================================================================
	// ======================================================================================================
	
	
	public function search($data){
		$r = $this->model->where('number', '=', $data['search'])->order_by('id', 'DESC')->find();
		
		if ($r->loaded()){
			$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
				'controller' => $this->request->controller,
				'action' => 'view',
				'id' => $r->id
			));
		}
		else{
			$this->post_redirect .= '?s=no';
		}
	}
	
	protected function delete_track($data){
		$this->model->delete($data['id']);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
		));
	}
	
	protected function change_track($data){
		$this->model->find($data['id']);
		$this->model->change_track($data);

		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'view',
			'id' => $data['id']
		));
	}
	
	protected function add_track($data){
		$r = $this->model->where('number', '=', $data['number'])->find();
		$data['user_id'] = ($r->user_id)? $r->user_id: 0;
		
		if ($data['user_id'])
			$this->send_user_message($data);
		
		$this->model->add_track($data);
		
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
//			'id' => $this->current_id
		));
	}
	
	protected function add_reference($data){
		$m = ORM::factory('widget_cargo_'.$data['reference']);
		$m->values($data)->save();

		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => $data['reference'],
		));
	}
	
	protected function save_reference($data){
		$m = ORM::factory('widget_cargo_'.$data['reference']);
		foreach($data['value'] as $id => $item){
			$m->clear();
			if (isset($data['delete'][$id])){
				$m->delete($id);
			}
			else{
				$m->find($id);
				$m->value = $item;
				$m->save();
			}
		}
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => $data['reference'],
		));
	}

	protected function send_user_message($data){
		$user = ORM::factory('widget_user');
		$item = $user->find($data['user_id']);
		Email::send('info@smlogistic.ru', $item->mail, 'Смена статуса груза на сайте smlogistic.ru', (string)GE::view('modules/office/sender', $data, TRUE), TRUE);
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