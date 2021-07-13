<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Error extends Template{

	public function __construct($request){
		$request->headers['Content-Type'] = 'text/html; charset=utf-8';
		parent::__construct($request);
	}
	
    public function action_404(){
        $this->request->status = 404;
        $this->request->headers['HTTP/1.1'] = '404';
        $this->content = View::factory('smarty:errors/404')
							->set('path', $this->request->param('path'));
    }
 
    public function action_403(){
        $this->request->status = 403;
        $this->request->headers['HTTP/1.1'] = '403';
        $this->content = View::factory('smarty:errors/403')
							->set('path', $this->request->param('path'));
    }
 
    public function action_500(){
        $this->request->status = 500;
        $this->request->headers['HTTP/1.1'] = '500';
        $this->content = View::factory('smarty:errors/500');
    }
	
}