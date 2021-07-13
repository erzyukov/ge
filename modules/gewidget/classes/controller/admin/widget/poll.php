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
class Controller_Admin_Widget_Poll extends Manage {
	
	protected $view_dir = 'smarty:admin/widget/poll/';
	
	protected $question_id = NULL;
	
	protected function init(){
		$this->model = ORM::factory('widget_poll_question');
		$id = $this->request->param('id');
		$this->sitemap = Sitemap::instance()->get_sitemap('module_id');
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
			'id' => $id
		));
		parent::init();
	}
	
	public function action_edit(){
		$data = array();
		$data['list'] = $this->model->find_all();
		$this->content = View::factory($this->view_dir.'cover', $data);
	}
	
	public function action_answer(){
		
		$data = array();
		
		$answers = ORM::factory('wpoll_question', $this->question_id)->answers->find_all();
		$data['list'] = '';
		foreach ($answers as $answer){
			$a = $answer->as_array();
			$data['list'] .= View::factory($this->view_dir.'answer', $a);;
		}
		
		$this->content = View::factory($this->view_dir.'answer_cover', $data);
	}
	
	public function add_question($data){
		$validate = $this->model->validate_create($data);
		if (! $validate->check()){
			// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
			// TODO Добавить систему сообщений
			return false;
		}
		$data = $validate->as_array();
		$id = $this->model->add_question($data);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
			'id' => $id
		));
	}
	
	
	protected function edit_question($data){

		$question = ORM::factory('wpoll_question');
		
		foreach($data['caption'] as $id => $item){
			
			if (isset($data['delete_list'][$id])){
				$question->delete($id);
				continue;
			}
			
			$save['caption'] = $item;
			$save['isactive'] = (isset($data['active_list'][$id]))? 1 : 0;
			
			$validate = $question->validate_create($save);
			if ($validate->check()){
				$save = Arr::overwrite($save, $validate->as_array());
				$question->find($id);
				$question->values($save);
				$question->save();
			}
		}
		
	}

	protected function add_answer($data){
		
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'answer',
			'p1' => $this->question_id,
		));
		
		$answer = ORM::factory('wpoll_answer');
		
		$validate = $answer->validate_create($data);
		if ($validate->check()){
			$data = Arr::overwrite($data, $validate->as_array());
			$data['question_id'] = $this->question_id;
			$answer->values($data);
			$answer->save();
		}
		
	}
	
	protected function edit_answer($data){

		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'answer',
			'p1' => $this->question_id,
		));
		
		$answer = ORM::factory('wpoll_answer');
		
		foreach($data['caption'] as $id => $item){
			
			if (isset($data['delete_list'][$id])){
				$answer->delete($id);
				continue;
			}
			
			$save['caption'] = $item;
			
			$validate = $answer->validate_create($save);
			if ($validate->check()){
				$save = Arr::overwrite($save, $validate->as_array());
				$answer->find($id);
				$answer->values($save);
				$answer->save();
			}
		}
		
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



