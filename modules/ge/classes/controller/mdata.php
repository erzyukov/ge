<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Mdata extends Controller {

	protected $module;
	protected $type = 'tpl';

	protected $template_dir;
	
	public function __construct($request){
		$request->headers['Content-Type'] = 'text/html; charset=utf-8';
		parent::__construct($request);
	}
	
	public function before(){
		$this->type = $this->request->param('type');
		$request = Request::factory($this->request->param('path'));
		$name = $request->param('module');

		$this->module = Generator::factory($name);
		$this->module->set_lang($request->param('lang'));
		$this->module->set_id($request->param('id', NULL));
		$this->module->set_page($request->param('page', NULL));
		$this->module->set_limit($request->param('limit', NULL));

		$template = $request->param('template', NULL);
		if ($template)
			$this->template_dir = 'custom/'.$template.'/';
		else
			$this->template_dir = 'modules/'.$name.'/';
	}
	
    public function action_content(){
    	$result = '';
    	$data = $this->module->content();

    	switch ($this->type){
    		case 'json':
    			$result = json_encode($data);
    			break;
    		case 'tpl':
    			$result = GE::view($this->template_dir.$this->module->get_template(), $data, TRUE);
    			break;
    	}
    	
    	$this->request->response = $result;
    }
    
	public function action_title(){
    	$this->request->response = $this->module->title();
    }
    
	public function action_navigation(){
    	$this->request->response = $this->module->navigation();
    }
    
	public function action_menu(){
    	$this->request->response = $this->module->description();
    }
    
	public function action_keywords(){
    	$this->request->response = $this->module->keywords();
    }
    
	public function action_description(){
    	$this->request->response = $this->module->description();
    }
    
	public function action_js(){
    	$this->request->response = $this->module->js();
    }
    
	public function action_css(){
    	$this->request->headers['Content-Type'] = 'text/css; charset=utf-8';
		$this->request->response = $this->module->css();
    }
    
}