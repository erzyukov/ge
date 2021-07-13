<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Module extends Template {

	protected $module;
	protected $template_dir;
	
	public function __construct($request){
		$request->headers['Content-Type'] = 'text/html; charset=utf-8';
		parent::__construct($request);
	}
	
	protected function _initialize(){
		$name = $this->request->param('module');
		$this->module = Generator::factory($name);
		$this->module->set_lang($this->request->param('lang'));
		$this->module->set_id($this->request->param('id', NULL));
		$this->module->set_page($this->request->param('page', NULL));
		$this->module->set_limit($this->request->param('limit', NULL));

		$template = $this->request->param('template', NULL);
		if ($template)
			$this->template_dir = 'custom/'.$template.'/';
		else
			$this->template_dir = 'modules/'.$name.'/';
			
		// устанавливаем дополнительные скрипты для раздела, если есть
		$path = Kohana::config('ge.js_dir').$name.'/main.js';
		if (is_file($_SERVER['DOCUMENT_ROOT'].$path))
			$this->add_js($path);
		
		$link = URL::base().Route::get('module_data')->uri(array(
					'action' => 'js',
					'path' => $this->request->uri,
				));
		$this->add_js($link);

		// устанавливаем дополнительные стили для раздела, если есть
		$path = Kohana::config('ge.css_dir').$name.'/main.css';
		if (is_file($_SERVER['DOCUMENT_ROOT'].$path))
			$this->add_css($path);
//		$link = 'customuser.css?p='.$this->request->uri;

		$link = URL::base().Route::get('module_data')->uri(array(
					'action' => 'css',
					'path' => $this->request->uri,
				));
		$this->add_css($link);

	}
	
    public function action_index(){
    	$data = $this->module->content();
    	$this->content = GE::view($this->template_dir.$this->module->get_template(), $data, TRUE);

    	$navigation = $this->module->navigation($this->request->action);

    	foreach ($navigation as $r)
    		$this->add_navigation($r['title'], $r['url']);

    	$this->title = $this->module->title();
    }
    
	public function action_list(){
		
    	$data = $this->module->content();
    	$this->content = GE::view($this->template_dir.$this->module->get_template(), $data, TRUE);

//    	$this->navigation = $this->module->navigation($this->request->action);
		
	}

	public function action_parent(){
		
    	$data = $this->module->content();
    	$this->content = GE::view($this->template_dir.$this->module->get_template(), $data, TRUE);

//    	$this->navigation = $this->module->navigation($this->request->action);
		
	}
	
	public function action_short(){
    	throw new Kohana_Exception('Can\'t launch this function directly!');
    }
    
}