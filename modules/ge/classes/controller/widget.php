<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Widget extends Controller {

	protected $widget = NULL;
	protected $type = 'tpl';

	protected $template_dir;
	
	public function __construct($request){
		$request->headers['Content-Type'] = 'text/html; charset=utf-8';
		parent::__construct($request);
		$this->_initialize();
	}
	
	protected function _initialize(){
		$name = $this->request->param('widget');
		$this->type = $this->request->param('type');
		$this->widget = Widget::factory($name);
		$this->widget->set_lang($this->request->param('lang'));
		$param = $this->get_param();
		foreach($param as $num => $value){
			$this->widget->set_param($num, $value);
		}
		$this->template_dir = 'widget/'.$name.'/';
	}
	
	protected function get_param(){
		$result = array();
		for ($i = 1; $i <= 10; $i++){
			$p = $this->request->param('p'.$i, NULL);
			if ($p)
				$result[$i] = $p;
		}
		return $result;
	}
	
    public function action_index(){
    	$result = '';
    	$data = $this->widget->data();

    	switch ($this->type){
    		case 'json':
    			$result = json_encode($data);
    			break;
    		case 'tpl':
    			$result = GE::view($this->template_dir.$this->widget->get_template(), array('data' => $data), TRUE);
    			break;
    	}
    	
    	$this->request->response = $result;
    }

    
}