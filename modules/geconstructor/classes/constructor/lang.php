<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Класс для настройки сайта. Управление языками сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class Constructor_Lang {

	/**
	 * Создает новую языковую версию сайта
	 * 
	 * @param array $data
	 */
	public static function create_lang($data){
		
		$lang = ORM::factory('lang');
		
		$post = $lang->validate_create($data);
		
		if ($post->check()){
		
			$lang->values($post);
			$lang->save();
			
		}

		return $lang->uri;
	}
	
	/**
	 * Изменяет настройки языка
	 * 
	 * @param array $data
	 */
	public static function edit_lang($data){
	
		$lang = ORM::factory('lang', $data['id']);
		
		$post = $lang->validate_edit($data);
		
		if ($post->check()){
		
			$lang->values($post);
			$lang->save();
			
		}
		
		return $lang->uri;
	}
	
	/**
	 * Удаляет языковую версию сайта
	 * 
	 * @param array $data
	 */
	public static function delete_lang($data){
	
		$lang = ORM::factory('lang', array('short' => $data['short']));
		$default = GE::lang('uri');
		if ($data['short'] != $default){
			$lang->delete();
			$lang->save();
			return $default;
		}
		
		return $data['short'];
	}
	
}