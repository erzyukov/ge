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
class Controller_Admin_Module extends Manage {

	protected $module;
	
	public function __construct($request){
		$request->headers['Content-Type'] = 'text/html; charset=utf-8';
		parent::__construct($request);
	}

	protected function init(){
		$this->module = Managegenerator::factory($this->request->param('module'));
		$this->module->set_id($this->request->param('id', NULL));
		
		parent::init();
		
		$this->module->set_lang($this->lang_id);
	}
	
	public function action_index(){
		
		$this->content = 'Здесь можно будет отобразить сводную информацию по всем модулям';
		
	}

	public function action_list(){
		
		$this->content = $this->module->content($this->request->action);
		$this->navigation = $this->module->navigation($this->request->action);
		
	}

	public function action_edit(){

		$this->content = $this->module->content($this->request->action);
		$this->navigation = $this->module->navigation($this->request->action);
		
	}

	public function action_parent(){

		$this->content = $this->module->content($this->request->action);
		$this->navigation = $this->module->navigation($this->request->action);
		
	}
	
	protected function post_action($post){

//echo Kohana::debug($this->module); die;
		
		if (isset($post['action'])){
			if (method_exists($this->module, $post['action'])){
				$action = $post['action'];
				unset($post['action']);
				$redirect = $this->module->$action($post, $this->lang_id, $this->request->action);
				// Здесь необходимо сделать проверку на наличие сообщений
				// т.к. могла возникнуть ошибка, если нет ошибок, перенаправляем куда наsдо
// TODO добавить обработчик сообщений
				//if (!$this->GetMessageCount())
				Request::instance()->redirect($redirect);
			}
		}
		
	}
	

	
}