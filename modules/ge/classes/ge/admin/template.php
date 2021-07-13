<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Надстройка над кохановским контроллером-шаблонизатором.
	 * Организатор структуры индексных шаблонов сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class GE_Admin_Template extends Controller_Template {

	public $template = 'smarty:admin/index';
	
	public $title = 'Site Title';
	public $navigation = '';
	public $message = '';
	public $content = '';
	public $menu = '';
	public $lang_list = array();
	public $module_menu = '';
	public $widget_menu = '';
	private $_message = array();
	private $_title = array();
	private $_navigation = array();
	
	public function __construct($request)
	{
		//Request::instance()
		$request->headers['Cache-Control'] = 'no-store, no-cache, must-revalidate';
		$request->headers['Expires'] = date("r");
		parent::__construct($request);
		$this->add_title('Панель Управления Сайтом');
		$this->add_navigation('GE2', '/');
		$this->lang_list = GE::get_lang();
		$this->init();
	}
	
	protected function init(){}
	
	public function before(){
		parent::before();

		$this->template
			->bind('title', $this->title)
			->bind('navigation', $this->navigation)
			->bind('message', $this->message)
			->bind('content', $this->content)
			->bind('menu', $this->menu)
			->bind('lang_list', $this->lang_list)
			->bind('module_menu', $this->module_menu)
			->bind('widget_menu', $this->widget_menu);
	}
	
	public function after(){
		$this->navigation = $this->navigation_build();

		$this->title = $this->var_build($this->_title);
		
		$this->module_menu = $this->get_module_menu();
		$this->widget_menu = $this->get_widget_menu();
		
// TODO Реализовать систему сообщений		
		$this->message = $this->message_build();
		
		//$this->debug = View::factory('profiler/stats');
		parent::after();
	}
	
	protected function get_module_menu(){}
	
	protected function get_widget_menu(){}
	
	
	protected function add_title($title){
		$this->_title[] = $title;
	}
	
	protected function add_navigation($title, $url){
		$this->_navigation[] = array('title' => $title, 'href' => $url);
	}
	
	protected function navigation_build(){
		$result = '';
		if (count($this->_navigation) > 1){
			$list = array();
			foreach ($this->_navigation as $data) {
				$list[] = View::factory('accessory/navigation_list', $data);
			}
			$result = View::factory('accessory/navigation', array('content' => implode(Gengine::config('site.navigation_sep'), $list)));
		}
		return $result;
	}
	
	protected function message_build(){
		return $this->message;
	}
	
	protected function var_build(array $var, $separator = ','){
		$mem = 0;
		foreach ( $var as $index => $element){
			if (substr($element, 0, 1) == '^'){
				$mem = $index;
				$var[$index] = substr($element, 1);
			}
		}
		for ( $i = 0; $i < $mem; $i++ ){
			unset($var[$i]);
		}
		return implode($separator, $var);
	}
	
	
} // End Gengine_Admin_Template