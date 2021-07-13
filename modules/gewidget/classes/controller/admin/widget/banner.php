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
class Controller_Admin_Widget_Banner extends Manage {
	
	protected $view_dir = 'smarty:admin/widget/banner/';
	
	protected $id = NULL;
	
	protected function init(){
		$this->model = ORM::factory('widget_banner_list');
		$this->id = $this->request->param('id', NULL);
//		if ($this->request->param('p1'))
//			$this->question_id = $this->request->param('p1');
//		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
//			'controller' => $this->request->controller,
//			'action' => 'edit',
//		));
		parent::init();
	}

	public function add_banner($data){
		$validate = $this->model->validate_create($data);
		if (! $validate->check()){
			// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
			// TODO Добавить систему сообщений
			return false;
		}
		$data = $validate->as_array();
		$id = $this->model->add_banner($data);
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'item',
			'id' => $id
		));
	}
	
	public function edit_list($data){
		
//		_d($data); die;
		
		foreach ($data['title_list'] as $id => $title){
			
			$this->model->find($id);
			
			// если есть на удаление - удаляем, из карты сайта - тоже
			if (isset($data['delete_list'][$id])){
				$this->model->delete($id);
				continue;
			}

			$update = array();
			$update['caption'] = $title;
			$update['position'] = $data['position_list'][$id];
			$update['isactive'] = (isset($data['active_list'][$id])) ? 1 : 0;
			
			$validate = $this->model->validate_create($update);
			if ( ! $validate->check()){
				// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
				// TODO Добавить систему сообщений
				continue;
			}
			$update = Arr::overwrite($update, $validate->as_array());
			
			$this->model->values($update);
			$this->model->save();

		}
		
		
		
		
		
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => 'edit',
			'id' => NULL
		));
	}
	
	public function edit_banner($data){
		$file = $_FILES['file'];

		$this->model->find($this->id);
		$validate = $this->model->validate_create($data);
		if (! $validate->check()){
			// TODO Здесь добавляем сообщение о том что что-то прошло не верно!
			// TODO Добавить систему сообщений
			return false;
		}
		$data = Arr::merge($data, $validate->as_array());
		$data['isactive'] = (isset($data['isactive'])) ? 1 : 0;

		if (Upload::not_empty($file)){
			$path = $_SERVER['DOCUMENT_ROOT'].Kohana::config('banner.path');
			GE::test_dir($path);
			Upload::save($file, $file['name'], $path, 0755);
			$data['path'] = Kohana::config('banner.path').$file['name'];
		}
		
		$this->model->edit_banner($data);
		
		$this->post_redirect = URL::base().Route::get('admin_widget')->uri(array(
			'controller' => $this->request->controller,
			'action' => (isset($data['_back']))? 'edit': 'item',
			'id' => (isset($data['_back']))? NULL: $this->model->id
		));
		
	}
	
	public function action_edit(){
		
		$data = array();

		$list = $this->model->order_by('position')->find_all();
		foreach($list as $r){
			$banner = $r->as_array();
			$banner['_edit_href'] = URL::base().Route::get('admin_widget')->uri(array(
				'controller' => $this->request->controller,
				'action' => 'item',
				'id' => $r->id
			));
			$banner['_checked'] = ($r->isactive) ? 'checked="true"': '';
			$banner[''] = '';
			$data['list'][] = $banner;
		}
//		foreach ($questions as $question){
//			$q = $question->as_array();
//			$q['_list_href'] = URL::base().Route::get('admin_widget')->uri(array(
//				'controller' => $this->request->controller,
//				'action' => 'answer',
//				'p1' => $q['id']
//			));
//			$q['_checked'] = ($q['isactive']) ? 'checked="true"': '';
//			$data['list'] .= View::factory($this->view_dir.'question', $q);;
//		}
//		
//		$this->content = View::factory($this->view_dir.'cover', $data);
		
		$this->content = View::factory($this->view_dir.'list', $data);
	}

	public function action_item(){
		
		$data = array();
		$data = $this->model->find($this->id)->as_array();

		$data['type_list'] = Kohana::config('banner.type');
		$data['isactive'] = ($data['isactive'] == 1)? 'checked="true"': '';
//		$data['sitemap'] = Sitemap::instance()->get_tree_caption_list('&nbsp;&nbsp;&nbsp;&nbsp;');
		
		$this->content = View::factory($this->view_dir.'item', $data);
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



