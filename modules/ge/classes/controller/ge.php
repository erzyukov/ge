<?php defined('SYSPATH') or die('No direct script access.');

	class Controller_Ge extends Kohana_Controller {
		
		public function action_version(){
			$this->request->response = GE::ver();
		}
		
	}
