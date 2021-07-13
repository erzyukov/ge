<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Базовый клас панели администрирования.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
abstract class GE_Admin_Manage extends Managetemplate {
	
	public $auth_required = TRUE;
	public $post_redirect = 'module';

	protected $lang_id;
	protected $session = NULL;
	
	protected function init(){
		parent::init();

		$this->session = Session::instance('database');
		// здесь проверяем залогинен ли пользователь, если нет, посылаем :)
		if ($this->auth_required !== FALSE AND Manageauth::instance()->logged_in() === FALSE){
			Request::instance()->redirect(URL::base().'admin/login');
		}
		
		$this->lang_id = (int) $this->session->get('lang_id', GE::lang('id'));

		$resolve = Kohana::config('lang.resolve');
		$lang_uri = GE::lang_list($this->lang_id);
		I18n::$lang = $resolve[$lang_uri];
		
		if (!empty($_POST))
			$this->post_action($_POST);
	}
	
	protected function post_action($post){/*перекрываем в дочерник классах*/}
	
	public function action_chlang(){
		if (isset($_GET['lang_select']))
			$this->session->set('lang_id', $_GET['lang_select']);
		Request::instance()->redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function before(){
		parent::before();
		
		$this->template
			->bind('cur_lang', $this->lang_id);
		

	}
	
	public function after(){
		
		//$this->debug = View::factory('profiler/stats');
		parent::after();
	}

	protected function get_module_menu(){
		
		$data = array();
		foreach (GE::modules() as $i => $module){
			$show = true;

			try {Generator::factory($module['name']);}
			catch (Exception $e){$show = false;}
			
			if ($show AND $module['parent_id'] == 0){
				$lang_id = (isset($module['translate'][$this->lang_id])) ? $this->lang_id : GE::lang('id');  
				$data['list'][$i]['title'] = $module['translate'][$lang_id]['title'];
				$data['list'][$i]['href'] = $this->get_module_url($module['name'], 'list');
			}
		}
		
		return GE::view('admin/accessory/module_menu', $data, FALSE);
	}

	public static function get_module_url($module, $action, $id = 0){
		$url = '';

		$map = Sitemap::instance()->get_sitemap(( (int)$module ) ? 'module_id': 'module');

		if (isset($map[$module][$id])){
			$item = $map[$module][$id];
			
			$url = URL::base().Route::get('admin_module')->uri(array(
				'module' => $item['name'],
				'action' => $action,
				'id' => ($item['item_id'])? $item['item_id']: NULL
			));
		}
		else if (isset($map[$module])){
			$item = $map[$module][0];
			
			$url = URL::base().Route::get('admin_module')->uri(array(
				'module' => $item['name'],
				'action' => $action,
				'id' => ($id)? $id: NULL
			));
		}
		else{
			$url = '/error/404';
		}
		
		return $url;
	}
	
	protected function get_widget_menu(){

		$data = array();
		foreach (GE::widgets() as $i => $widget){
			$data['list'][$i]['title'] = $widget['title'];
			$data['list'][$i]['href'] = $this->get_widget_url($widget['name']);
			
			
		}

		return GE::view('admin/accessory/widget_menu', $data, FALSE);
	}
	
	protected function get_widget_url($widget, $action = 'edit', $id = 0){

		$url = URL::base().Route::get('admin_widget')->uri(array(
				'controller' => $widget,
				'action' => 'edit',
				'id' => ($id)? $id: NULL
			));
		
		return $url;
	}
	
	
} // End Gengine_Admin_Manage