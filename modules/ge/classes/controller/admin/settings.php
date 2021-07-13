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
class Controller_Admin_Settings extends Manage {
	
	public $post_redirect = '/admin/settings';
	
	public function action_index(){
		$data = array();
		
		$this->content = GE::view('admin/settings/cover', $data, FALSE);
	}
	
	protected function change_password($data){
		$auth = Manageauth::instance();

		if ( $auth->password($auth->get_user()) == $auth->hash_password(trim($data['old_password']))){
			$user = $auth->get_user();
			$user->change_password($data);
			$user->save();
		}
		
		return $this->post_redirect;
	}

	
	protected function post_action($post){

		if (isset($post['action'])){
			if (method_exists($this, $post['action'])){
				$action = $post['action'];
				unset($post['action']);
				$redirect = $this->$action($post);
				Request::instance()->redirect($redirect);
			}
		}
		
	}
		
}